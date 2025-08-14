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

namespace Civi\Reimbursement\Form;

use Civi\RemoteTools\Form\FormSpec\DataTransformerInterface;

final class ReimbursementDataTransformer implements DataTransformerInterface {

  public function toEntityValues(array $formData, ?array $currentEntityValues, ?int $contactId): array {
    $today = \CRM_Utils_Time::date('Y-m-d');

    $entityValues = ['expenses' => []];
    foreach ($formData as $fieldName => $value) {
      if (str_starts_with($fieldName, 'expenses_')) {
        assert(is_array($value));
        [, $expenseTypeId] = explode('_', $fieldName);
        foreach ($value as $expense) {
          assert(is_array($expense));
          $expense['type_id'] = $expenseTypeId;
          $expense['status_id:name'] = 'Pending';
          $expense['contact_id'] = $contactId;
          $expense['source_contact_id'] = $contactId;
          $expense['date'] = $today;
          // @phpstan-ignore offsetAccess.nonOffsetAccessible
          $entityValues['expenses'][] = $expense;
        }
      }
      elseif (!str_starts_with($fieldName, '_')) {
        $entityValues[$fieldName] = $value;
      }
    }

    return $entityValues;
  }

}
