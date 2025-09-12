<?php
/*
 * Copyright (C) 2024 SYSTOPIA GmbH
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation in version 3.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types = 1);

namespace Civi\Reimbursement;

use Civi\API\Exception\UnauthorizedException;
use Civi\Reimbursement\Form\ReimbursementFormSpecFactory;
use Civi\Reimbursement\Form\SelectCaseTypeFormSpecFactory;
use Civi\RemoteTools\Api4\Query\CompositeCondition;
use Civi\RemoteTools\Api4\Query\ConditionInterface;
use Civi\RemoteTools\Api4\Query\Join;
use Civi\RemoteTools\EntityProfile\AbstractRemoteEntityProfile;
use Civi\RemoteTools\EntityProfile\Authorization\GrantResult;
use Civi\RemoteTools\Form\FormSpec\FormSpec;
use CRM_Reimbursement_ExtensionUtil as E;

final class ReimbursementProfile extends AbstractRemoteEntityProfile {

  public const NAME = 'reimbursement';

  public const ENTITY_NAME = 'Case';

  public const REMOTE_ENTITY_NAME = 'RemoteCase';

  private CaseTypeConfigManager $caseTypeConfigManager;

  private ExpenseLoader $expenseLoader;

  private ExpensePersister $expensePersister;

  /**
   * @var list<array<string, mixed>>
   */
  private array $expenses = [];

  private ReimbursementFormSpecFactory $formSpecFactory;

  private SelectCaseTypeFormSpecFactory $selectCaseTypeFormSpecFactory;

  public function __construct(
    CaseTypeConfigManager $caseTypeConfigManager,
    ExpenseLoader $expenseLoader,
    ExpensePersister $expensePersister,
    ReimbursementFormSpecFactory $formSpecFactory,
    SelectCaseTypeFormSpecFactory $selectCaseTypeFormSpecFactory
  ) {
    $this->caseTypeConfigManager = $caseTypeConfigManager;
    $this->expenseLoader = $expenseLoader;
    $this->expensePersister = $expensePersister;
    $this->formSpecFactory = $formSpecFactory;
    $this->selectCaseTypeFormSpecFactory = $selectCaseTypeFormSpecFactory;
  }

  public function getEntityName(): string {
    return self::ENTITY_NAME;
  }

  public function getName(): string {
    return self::NAME;
  }

  public function getRemoteEntityName(): string {
    return self::REMOTE_ENTITY_NAME;
  }

  public function getFilter(string $actionName, ?int $contactId): ?ConditionInterface {
    return NULL;
  }

  public function getJoins(string $actionName, ?int $contactId): array {
    return [
      Join::new('CaseContact', 'caseContact', 'INNER', CompositeCondition::fromFieldValuePairs([
        'caseContact.case_id' => 'id',
        'caseContact.contact_id' => $contactId,
      ])),
    ];
  }

  public function isCreateGranted(array $arguments, ?int $contactId): GrantResult {
    if (NULL === $contactId) {
      return GrantResult::newDenied();
    }

    if (!isset($arguments['type'])) {
      return [] === $this->caseTypeConfigManager->getForNewCases()
        ? GrantResult::newDenied() : GrantResult::newPermitted();
    }

    $caseTypeConfig = is_string($arguments['type'])
      ? $this->caseTypeConfigManager->getByCaseTypeName($arguments['type']) : NULL;

    return NULL === $caseTypeConfig?->getInitialCaseStatusId() ? GrantResult::newDenied() : GrantResult::newPermitted();
  }

  public function isUpdateGranted(?array $entityValues, ?int $contactId): GrantResult {
    if (NULL === $entityValues) {
      return GrantResult::newDenied();
    }

    // @phpstan-ignore argument.type
    $caseTypeConfig = $this->caseTypeConfigManager->getByCaseTypeId($entityValues['case_type_id']);

    return NULL === $caseTypeConfig ? GrantResult::newDenied() : GrantResult::newPermitted();
  }

  public function getSelectFieldNames(array $select, string $actionName, array $remoteSelect, ?int $contactId): array {
    $select[] = 'case_type_id';
    $select[] = 'custom.*';

    return $select;
  }

  public function getCreateFormSpec(array $arguments, array $entityFields, ?int $contactId): FormSpec {
    if (!isset($arguments['type'])) {
      $configs = $this->caseTypeConfigManager->getForNewCases();
      if (count($configs) === 1) {
        return $this->formSpecFactory->createFormSpec($configs[0], NULL);
      }

      return $this->selectCaseTypeFormSpecFactory->createFormSpec($configs);
    }

    assert(is_string($arguments['type']));
    $caseTypeConfig = $this->caseTypeConfigManager->getByCaseTypeName($arguments['type']);
    assert(NULL !== $caseTypeConfig);

    return $this->formSpecFactory->createFormSpec($caseTypeConfig, NULL);
  }

  public function getUpdateFormSpec(array $entityValues, array $entityFields, ?int $contactId): FormSpec {
    // @phpstan-ignore argument.type
    $caseTypeConfig = $this->caseTypeConfigManager->getByCaseTypeId($entityValues['case_type_id']);
    if (NULL === $caseTypeConfig) {
      throw new UnauthorizedException(E::ts('Case type is not configured for reimbursement.'));
    }

    // @phpstan-ignore argument.type
    $entityValues['expenses'] = $this->expenseLoader->getExpensesByCaseId($entityValues['id']);

    return $this->formSpecFactory->createFormSpec($caseTypeConfig, $entityValues);
  }

  public function onPreCreate(
    array $arguments,
    array &$entityValues,
    array $entityFields,
    FormSpec $formSpec,
    ?int $contactId
  ): void {
    // @phpstan-ignore assign.propertyType
    $this->expenses = $entityValues['expenses'];
    unset($entityValues['expenses']);
  }

  /**
   * @throws \CRM_Core_Exception
   */
  public function onPostCreate(
    array $arguments,
    array $entityValues,
    array $entityFields,
    FormSpec $formSpec,
    ?int $contactId
  ): void {
    // @phpstan-ignore argument.type
    $this->expensePersister->persistExpenses($this->expenses, $entityValues['id'], $contactId);
  }

  public function onPreUpdate(
    array &$newValues,
    array $oldValues,
    array $entityFields,
    FormSpec $formSpec,
    ?int $contactId
  ): void {
    // @phpstan-ignore assign.propertyType
    $this->expenses = $newValues['expenses'];
    unset($newValues['expenses']);
  }

  public function onPostUpdate(
    array $newValues,
    array $oldValues,
    array $entityFields,
    FormSpec $formSpec,
    ?int $contactId
  ): void {
    // @phpstan-ignore argument.type
    $this->expensePersister->persistExpenses($this->expenses, $newValues['id'], $contactId);
  }

}
