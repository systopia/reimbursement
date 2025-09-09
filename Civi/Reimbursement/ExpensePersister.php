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
use Civi\RemoteTools\Api4\Query\CompositeCondition;
use Civi\RemoteTools\Helper\AttachmentsPersisterInterface;

/**
 * @phpstan-import-type attachmentT from AttachmentsPersisterInterface
 */
final class ExpensePersister {

  private Api4Interface $api4;

  private AttachmentsPersisterInterface $attachmentsPersister;

  private ExpenseLoader $expenseLoader;

  public function __construct(
    Api4Interface $api4,
    AttachmentsPersisterInterface $attachmentsPersister,
    ExpenseLoader $expenseLoader
  ) {
    $this->api4 = $api4;
    $this->attachmentsPersister = $attachmentsPersister;
    $this->expenseLoader = $expenseLoader;
  }

  /**
   * @param list<array<string, mixed>> $expenses
   *
   * @throws \CRM_Core_Exception
   */
  public function persistExpenses(array $expenses, int $caseId, ?int $contactId): void {
    /** @var array<int, array<string, mixed>> $currentExpensesById */
    $currentExpensesById = array_column($this->expenseLoader->getExpensesByCaseId($caseId), NULL, 'id');

    $expenseIds = [];
    foreach ($expenses as $expense) {
      if (isset($expense['id']) && !isset($currentExpensesById[$expense['id']])) {
        unset($expense['id']);
      }

      $amount = $expense['amount'];
      assert(is_float($amount) || is_int($amount));
      unset($expense['amount']);

      $attachments = $expense['attachments'];
      assert(is_array($attachments));
      unset($expense['attachments']);

      $expense['case_id'] = $caseId;
      if (isset($expense['id'])) {
        $expenseId = $expense['id'];
        assert(is_int($expenseId));
        $this->api4->updateEntity('Expense', $expenseId, $expense)->single();
      }
      else {
        /** @var int $expenseId */
        $expenseId = $this->api4->createEntity('Expense', $expense)->single()['id'];
      }

      $this->persistExpenseLine($amount, $expenseId);
      // @phpstan-ignore argument.type
      $this->persistAttachments($attachments, $expenseId, $contactId);

      $expenseIds[] = $expenseId;
    }

    /** @var list<int> $deletedExpenseIds */
    $deletedExpenseIds = array_diff(array_keys($currentExpensesById), $expenseIds);
    if ([] !== $deletedExpenseIds) {
      $this->api4->deleteEntities('Expense', Comparison::new('id', 'IN', $deletedExpenseIds));
    }
  }

  /**
   * @throws \CRM_Core_Exception
   */
  private function persistExpenseLine(int|float $amount, int $expenseId): void {
    /** @var list<array<string, mixed>> $currentExpenseLines */
    $currentExpenseLines = $this->api4->getEntities(
      'ExpenseLine',
      Comparison::new('expense_id', '=', $expenseId)
    )->getArrayCopy();

    if (isset($currentExpenseLines[0]['id'])) {
      /** @var int $expenseLineId */
      $expenseLineId = $currentExpenseLines[0]['id'];
      $this->api4->updateEntity('ExpenseLine', $expenseLineId, ['amount' => $amount]);

      if (count($currentExpenseLines) > 1) {
        // May happen if an additional expense line was added in CiviCRM.
        $this->api4->deleteEntities(
          'ExpenseLine',
          CompositeCondition::new(
            'AND',
            Comparison::new('expense_id', '=', $expenseId),
            Comparison::new('id', '!=', $expenseLineId)
          )
        );
      }
    }
    else {
      $this->api4->createEntity('ExpenseLine', [
        'expense_id' => $expenseId,
        'amount' => $amount,
      ]);
    }
  }

  /**
   * @param list<attachmentT> $attachments
   *
   * @throws \CRM_Core_Exception
   */
  private function persistAttachments(array $attachments, int $expenseId, ?int $contactId): void {
    $this->attachmentsPersister->persistAttachmentsFromForm('Expense', $expenseId, $attachments, $contactId);
  }

}
