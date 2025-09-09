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

use Civi\Api4\Expense;
use Civi\Api4\ExpenseLine;
use Civi\Reimbursement\Fixtures\CaseFixture;
use Civi\Reimbursement\Fixtures\ContactFixture;
use Civi\Reimbursement\Fixtures\ExpenseFixture;
use Civi\Reimbursement\Fixtures\ExpenseLineFixture;
use Civi\Reimbursement\Fixtures\ExpenseTypeFixture;
use Civi\RemoteTools\Api4\Api4;
use Civi\RemoteTools\Helper\AttachmentsLoaderInterface;
use Civi\RemoteTools\Helper\AttachmentsPersisterInterface;

/**
 * @covers \Civi\Reimbursement\ExpensePersister
 *
 * @group headless
 */
final class ExpensePersisterTest extends AbstractReimbursementHeadlessTestCase {

  private AttachmentsPersisterInterface&\PHPUnit\Framework\MockObject\MockObject $attachmentsPersisterMock;

  private ExpensePersister $expensePersister;

  protected function setUp(): void {
    parent::setUp();
    $attachmentsLoaderMock = $this->createMock(AttachmentsLoaderInterface::class);
    $this->attachmentsPersisterMock = $this->createMock(AttachmentsPersisterInterface::class);
    $this->expensePersister = new ExpensePersister(
      Api4::getInstance(),
      $this->attachmentsPersisterMock,
      new ExpenseLoader(Api4::getInstance(), $attachmentsLoaderMock)
    );
  }

  public function testNew(): void {
    $contact = ContactFixture::addIndividualFixture();
    $case = CaseFixture::addFixture($contact['id']);
    ExpenseTypeFixture::addFixture(999, 'test');

    $expenseData = [
      'contact_id' => $contact['id'],
      'source_contact_id' => $contact['id'],
      'type_id' => 999,
      'status_id:name' => 'Pending',
      'date' => '2025-08-14',
      'amount' => 1.23,
      'attachments' => [['test_attachment']],
    ];

    $expenseId = NULL;
    $this->attachmentsPersisterMock->expects(static::once())->method('persistAttachmentsFromForm')
      ->willReturnCallback(function (
        string $entityName,
        int $entityId,
        array $attachments,
        ?int $contactId
      ) use (&$expenseId, $contact): void {
        static::assertSame('Expense', $entityName);
        $expenseId = $entityId;
        static::assertSame([['test_attachment']], $attachments);
        static::assertSame($contact['id'], $contactId);
      });
    $this->expensePersister->persistExpenses([$expenseData], $case['id'], $contact['id']);

    $persistedExpense = Expense::get(FALSE)->setSelect(['*', 'status_id:name'])->execute()->single();
    static::assertSame($case['id'], $persistedExpense['case_id']);
    static::assertSame($contact['id'], $persistedExpense['contact_id']);
    static::assertSame($contact['id'], $persistedExpense['source_contact_id']);
    static::assertSame(999, $persistedExpense['type_id']);
    static::assertSame('2025-08-14', $persistedExpense['date']);
    static::assertSame('Pending', $persistedExpense['status_id:name']);
    static::assertSame($expenseId, $persistedExpense['id']);

    $persistedExpenseLine = ExpenseLine::get(FALSE)
      ->addWhere('expense_id', '=', $persistedExpense['id'])
      ->execute()
      ->single();

    static::assertSame(1.23, $persistedExpenseLine['amount']);
  }

  public function testUpdate(): void {
    $contact1 = ContactFixture::addIndividualFixture();
    $contact2 = ContactFixture::addIndividualFixture();
    $case = CaseFixture::addFixture($contact1['id']);
    ExpenseTypeFixture::addFixture(888, 'test');
    ExpenseTypeFixture::addFixture(999, 'test2');
    $expense = ExpenseFixture::addFixture($case['id'], $contact1['id'], 'test', [
      'date' => '2025-08-14',
      'status_id:name' => 'Approved',
    ]);
    ExpenseLineFixture::addFixture($expense['id'], 1.23);

    $attachments = [['test_attachment']];
    $expenseData = [
      'id' => $expense['id'],
      'contact_id' => $contact2['id'],
      'source_contact_id' => $contact2['id'],
      'type_id' => 999,
      'status_id:name' => 'Pending',
      'amount' => 456,
      'attachments' => $attachments,
    ];

    $this->attachmentsPersisterMock->expects(static::once())->method('persistAttachmentsFromForm')
      ->with('Expense', $expense['id'], $attachments, $contact2['id']);
    $this->expensePersister->persistExpenses([$expenseData], $case['id'], $contact2['id']);

    $persistedExpense = Expense::get(FALSE)->setSelect(['*', 'status_id:name'])->execute()->single();
    static::assertSame($expense['id'], $persistedExpense['id']);
    static::assertSame($case['id'], $persistedExpense['case_id']);
    static::assertSame($contact2['id'], $persistedExpense['contact_id']);
    static::assertSame($contact2['id'], $persistedExpense['source_contact_id']);
    static::assertSame(999, $persistedExpense['type_id']);
    static::assertSame('Pending', $persistedExpense['status_id:name']);

    $persistedExpenseLine = ExpenseLine::get(FALSE)
      ->addWhere('expense_id', '=', $persistedExpense['id'])
      ->execute()
      ->single();

    static::assertSame(456.0, $persistedExpenseLine['amount']);
  }

  public function testPreviousExpenseRemoved(): void {
    $contact = ContactFixture::addIndividualFixture();
    $case = CaseFixture::addFixture($contact['id']);
    ExpenseTypeFixture::addFixture(999, 'test');
    $expense = ExpenseFixture::addFixture($case['id'], $contact['id'], 'test');
    ExpenseLineFixture::addFixture($expense['id'], 1.23);

    $expenseData = [
      'contact_id' => $contact['id'],
      'source_contact_id' => $contact['id'],
      'type_id' => 999,
      'status_id:name' => 'Pending',
      'amount' => 4.56,
      'attachments' => [['test_attachment']],
    ];

    $expenseId = NULL;
    $this->attachmentsPersisterMock->expects(static::once())->method('persistAttachmentsFromForm')
      ->willReturnCallback(function (
        string $entityName,
        int $entityId,
        array $attachments,
        ?int $contactId
      ) use (&$expenseId, $contact): void {
        static::assertSame('Expense', $entityName);
        $expenseId = $entityId;
        static::assertSame([['test_attachment']], $attachments);
        static::assertSame($contact['id'], $contactId);
      });
    $this->expensePersister->persistExpenses([$expenseData], $case['id'], $contact['id']);

    $persistedExpense = Expense::get(FALSE)->setSelect(['*', 'status_id:name'])->execute()->single();
    static::assertNotSame($expense['id'], $persistedExpense['id']);
    static::assertSame($case['id'], $persistedExpense['case_id']);
    static::assertSame(999, $persistedExpense['type_id']);
    static::assertSame('Pending', $persistedExpense['status_id:name']);
    static::assertSame($expenseId, $persistedExpense['id']);

    $persistedExpenseLine = ExpenseLine::get(FALSE)
      ->addWhere('expense_id', '=', $persistedExpense['id'])
      ->execute()
      ->single();

    static::assertSame(4.56, $persistedExpenseLine['amount']);
  }

  public function testAdditionalExpenseLinesRemoved(): void {
    $contact = ContactFixture::addIndividualFixture();
    $case = CaseFixture::addFixture($contact['id']);
    ExpenseTypeFixture::addFixture(999, 'test');
    $expense = ExpenseFixture::addFixture($case['id'], $contact['id'], 'test');
    ExpenseLineFixture::addFixture($expense['id'], 1.23);
    ExpenseLineFixture::addFixture($expense['id'], 123);

    $expenseData = [
      'id' => $expense['id'],
      'contact_id' => $contact['id'],
      'source_contact_id' => $contact['id'],
      'type_id' => 999,
      'status_id:name' => 'Pending',
      'amount' => 456,
      'attachments' => [],
    ];

    $this->attachmentsPersisterMock->expects(static::once())->method('persistAttachmentsFromForm')
      ->with('Expense', $expense['id'], [], $contact['id']);
    $this->expensePersister->persistExpenses([$expenseData], $case['id'], $contact['id']);

    $persistedExpense = Expense::get(FALSE)->setSelect(['*', 'status_id:name'])->execute()->single();
    static::assertSame($expense['id'], $persistedExpense['id']);
    static::assertSame($case['id'], $persistedExpense['case_id']);
    static::assertSame(999, $persistedExpense['type_id']);
    static::assertSame('Pending', $persistedExpense['status_id:name']);

    $persistedExpenseLine = ExpenseLine::get(FALSE)
      ->addWhere('expense_id', '=', $persistedExpense['id'])
      ->execute()
      ->single();

    static::assertSame(456.0, $persistedExpenseLine['amount']);
  }

}
