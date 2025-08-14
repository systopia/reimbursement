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

namespace Civi\Reimbursement;

use Civi\RemoteTools\Api4\Api4Interface;
use Civi\RemoteTools\Api4\Query\Comparison;

final class ExpenseLoader {

  private Api4Interface $api4;

  public function __construct(Api4Interface $api4) {
    $this->api4 = $api4;
  }

  /**
   * @return list<array<string, mixed>>
   *
   * @throws \CRM_Core_Exception
   */
  public function getExpensesByCaseId(int $caseId): array {
    /** @var list<array<string, mixed>> $expenses */
    $expenses = $this->api4->execute('Expense', 'get', [
      'select' => ['*', 'custom.*'],
      'where' => [['case_id', '=', $caseId]],
    ])->getArrayCopy();

    foreach ($expenses as $id => &$expense) {
      /** @var int $expenseId */
      $expenseId = $expense['id'];
      $expenseLine = $this->api4->getEntities(
        'ExpenseLine',
        Comparison::new('expense_id', '=', $expenseId)
      )->first();

      if (NULL === $expenseLine) {
        // Do not return expenses without expense line.
        unset($expenses[$id]);
        $expenses = array_values($expenses);
      }
      else {
        $expense['amount'] = $expenseLine['amount'];
      }
    }

    return $expenses;
  }

}
