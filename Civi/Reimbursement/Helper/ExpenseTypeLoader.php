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

final class ExpenseTypeLoader {

  private Api4Interface $api4;

  public function __construct(Api4Interface $api4) {
    $this->api4 = $api4;
  }

  /**
   * @return array<int, array{string, string}>
   *   Mapping of type id to tuples of name and label ordered by weight.
   *
   * @throws \CRM_Core_Exception
   */
  public function getExpenseTypes(): array {
    $returnTypes = [];

    /** @var array<int, array{value: string, name: string, label: string}> $types */
    $types = $this->api4->execute('OptionValue', 'get', [
      'select' => [
        'value',
        'name',
        'label',
      ],
      'where' => [
        ['option_group_id:name', '=', 'expense_type'],
      ],
      'orderBy' => [
        'weight' => 'ASC',
      ],
    ])->indexBy('value')->getArrayCopy();

    foreach ($types as $id => $type) {
      $returnTypes[$id] = [$type['name'], $type['label']];
    }

    return $returnTypes;
  }

}
