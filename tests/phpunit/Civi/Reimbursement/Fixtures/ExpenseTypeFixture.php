<?php
/*
 * Copyright (C) 2022 SYSTOPIA GmbH
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

namespace Civi\Reimbursement\Fixtures;

use Civi\Api4\OptionValue;

final class ExpenseTypeFixture {

  /**
   * @param array<string, mixed> $values
   *
   * @return array<string, mixed>
   *
   * @throws \CRM_Core_Exception
   */
  public static function addFixture(int $id, string $name, array $values = []): array {
    return OptionValue::create(FALSE)
      ->setValues($values + [
        'option_group_id.name' => 'expense_type',
        'name' => $name,
        'value' => $id,
        'label' => $name,
      ])->execute()->single();
  }

}
