<?php
/*
 * Copyright (C) 2025 SYSTOPIA GmbH
 *
 * This program is free software: you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License as published by the Free
 * Software Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types = 1);

namespace Civi\Reimbursement;

use Civi\Api4\ReimbursementCaseTypeConfig;
use Civi\RemoteTools\Api4\Api4Interface;
use Civi\RemoteTools\Api4\Query\Comparison;
use Civi\RemoteTools\Api4\Query\ConditionInterface;

final class CaseTypeConfigManager {

  private Api4Interface $api4;

  /**
   * @var array<int, \Civi\Reimbursement\CaseTypeConfigData>
   */
  private array $caseTypeConfigsById = [];

  /**
   * @var array<string, \Civi\Reimbursement\CaseTypeConfigData>
   */
  private array $caseTypeConfigsByName = [];

  public function __construct(Api4Interface $api4) {
    $this->api4 = $api4;
  }

  /**
   * @param array<string, 'ASC'|'DESC'> $orderBy
   *
   * @return list<CaseTypeConfigData>
   *
   * @throws \CRM_Core_Exception
   */
  public function getBy(ConditionInterface $condition, array $orderBy = []): array {
    /** @var list<array<string, mixed>> $dataList */
    $dataList = $this->api4->execute(ReimbursementCaseTypeConfig::getEntityName(), 'get', [
      'select' => ['*', 'case_type_id:name', 'case_type_id:label'],
      'where' => [$condition->toArray()],
      'orderBy' => $orderBy,
    ])->getArrayCopy();

    return array_map(function (array $data) {
      $data['writable_case_status_ids'] = array_map(
        // @phpstan-ignore argument.type
        fn(int|string $id) => (int) $id,
        // @phpstan-ignore argument.type
        $data['writable_case_status_ids'] ?? []
      );
      // @phpstan-ignore argument.type, argument.type
      $data['expense_type_ids'] = array_map(fn(int|string $id) => (int) $id, $data['expense_type_ids'] ?? []);

      // @phpstan-ignore argument.type
      $caseTypeConfig = new CaseTypeConfigData($data);
      $this->caseTypeConfigsById[$caseTypeConfig->getId()] = $caseTypeConfig;
      $this->caseTypeConfigsByName[$caseTypeConfig->getCaseTypeName()] = $caseTypeConfig;

      return $caseTypeConfig;
    }, $dataList);
  }

  /**
   * @throws \CRM_Core_Exception
   */
  public function getByCaseTypeId(int $caseTypeId): ?CaseTypeConfigData {
    return $this->caseTypeConfigsById[$caseTypeId] ??
      $this->getBy(Comparison::new('case_type_id', '=', $caseTypeId))[0] ?? NULL;
  }

  /**
   * @throws \CRM_Core_Exception
   */
  public function getByCaseTypeName(string $caseTypeName): ?CaseTypeConfigData {
    return $this->caseTypeConfigsByName[$caseTypeName]
      ?? $this->getBy(Comparison::new('case_type_id:name', '=', $caseTypeName))[0] ?? NULL;
  }

  /**
   * @return list<CaseTypeConfigData>
   *   Case type configs that can be used for new cases, i.e. with initial case
   *   status ID, ordered by case type label.
   *
   * @throws \CRM_Core_Exception
   */
  public function getForNewCases(): array {
    return $this->getBy(Comparison::new('initial_case_status_id', '!=', NULL), ['case_type_id:label' => 'ASC']);
  }

}
