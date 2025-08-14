<?php
/*
 * Copyright (C) 2025 SYSTOPIA GmbH
 *
 *  This program is free software: you can redistribute it and/or modify it under
 *  the terms of the GNU Affero General Public License as published by the Free
 *  Software Foundation, either version 3 of the License, or (at your option) any
 *  later version.
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

namespace Civi\Reimbursement\Helper;

use Civi\RemoteTools\Api4\Api4Interface;
use Civi\RemoteTools\Api4\Query\Comparison;
use Civi\RemoteTools\Api4\Query\CompositeCondition;
use Civi\RemoteTools\Api4\Query\ConditionInterface;

/**
 * @phpstan-import-type fieldT from \Civi\RemoteTools\Form\FormSpec\FormFieldFactoryInterface
 * @phpstan-import-type conditionT from ConditionInterface
 */
final class FieldsLoader {

  private Api4Interface $api4;

  private CustomFieldsHelper $customFieldsHelper;

  public function __construct(Api4Interface $api4, CustomFieldsHelper $customFieldsHelper) {
    $this->api4 = $api4;
    $this->customFieldsHelper = $customFieldsHelper;
  }

  /**
   * @param array<string, scalar|null> $values
   *
   * @phpstan-return list<fieldT>
   *
   * @throws \CRM_Core_Exception
   */
  public function getFields(
    string $entityName,
    array $values = [],
    ?ConditionInterface $condition = NULL,
  ): array {
    // @phpstan-ignore return.type
    return $this->api4->execute($entityName, 'getFields', [
      'loadOptions' => TRUE,
      'values' => $values,
      'where' => self::toWhere($condition),
    ])->getArrayCopy();
  }

  /**
   * @param array<string, scalar|null> $values
   *
   * @phpstan-return list<fieldT>
   *
   * @throws \CRM_Core_Exception
   */
  public function getNonCustomFields(
    string $entityName,
    array $values = [],
    ?ConditionInterface $condition = NULL,
  ): array {
    if (NULL === $condition) {
      $condition = Comparison::new('type', '!=', 'Custom');
    }
    else {
      $condition = CompositeCondition::new('AND', Comparison::new('type', '!=', 'Custom'), $condition);
    }

    return $this->getFields($entityName, $values, $condition);
  }

  /**
   * @param array<string, scalar|null> $values
   *
   * @phpstan-return list<fieldT>
   *   Fields ordered by weight.
   *
   * @throws \CRM_Core_Exception
   */
  public function getPublicCustomFields(
    string $entityName,
    array $values = [],
    ?ConditionInterface $condition = NULL,
  ): array {
    if (NULL === $condition) {
      $condition = Comparison::new('custom_field_id', '!=', NULL);
    }
    else {
      $condition = CompositeCondition::new('AND', Comparison::new('custom_field_id', '!=', NULL), $condition);
    }

    $fields = $this->getFields($entityName, $values, $condition);

    return $this->customFieldsHelper->getPublicFieldsOrderedByWeight($fields);
  }

  /**
   * @param \Civi\RemoteTools\Api4\Query\ConditionInterface|null $condition
   *
   * @phpstan-return array{}|array{conditionT}
   */
  private static function toWhere(?ConditionInterface $condition): array {
    if (NULL === $condition) {
      return [];
    }

    return [$condition->toArray()];
  }

}
