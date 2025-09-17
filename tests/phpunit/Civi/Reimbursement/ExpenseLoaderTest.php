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

use Civi\Reimbursement\Fixtures\CaseFixture;
use Civi\Reimbursement\Fixtures\ContactFixture;
use Civi\Reimbursement\Fixtures\ExpenseFixture;
use Civi\Reimbursement\Fixtures\ExpenseLineFixture;
use Civi\Reimbursement\Fixtures\ExpenseTypeFixture;
use Civi\RemoteTools\Api4\Api4;
use Civi\RemoteTools\Helper\AttachmentsLoaderInterface;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @covers \Civi\Reimbursement\ExpenseLoader
 *
 * @group headless
 */
final class ExpenseLoaderTest extends AbstractReimbursementHeadlessTestCase {

  private AttachmentsLoaderInterface&MockObject $attachmentsLoaderMock;

  /**
   * @var \Civi\Reimbursement\ExpenseLoader
   */
  private ExpenseLoader $expenseLoader;

  protected function setUp(): void {
    parent::setUp();
    $this->attachmentsLoaderMock = $this->createMock(AttachmentsLoaderInterface::class);
    $this->expenseLoader = new ExpenseLoader(new Api4(), $this->attachmentsLoaderMock);
  }

  public function testGetExpensesByCaseId(): void {
    $contact = ContactFixture::addIndividualFixture();
    $case = CaseFixture::addFixture($contact['id']);
    ExpenseTypeFixture::addFixture(999, 'test');
    $expense = ExpenseFixture::addFixture($case['id'], $contact['id'], 'test', ['date' => '2025-08-14']);

    // Expense without expense line isn't returned.
    static::assertSame([], $this->expenseLoader->getExpensesByCaseId($case['id']));

    $attachments = [['id' => 123]];
    $this->attachmentsLoaderMock->method('getAttachments')
      ->with('Expense', $expense['id'])
      ->willReturn($attachments);

    ExpenseLineFixture::addFixture($expense['id'], 1.23, ['description' => 'test']);
    $expenses = $this->expenseLoader->getExpensesByCaseId($case['id']);
    static::assertCount(1, $expenses);
    static::assertSame($expense['id'], $expenses[0]['id']);
    static::assertSame($case['id'], $expenses[0]['case_id']);
    static::assertSame($contact['id'], $expenses[0]['contact_id']);
    static::assertSame($contact['id'], $expenses[0]['source_contact_id']);
    static::assertSame(999, $expenses[0]['type_id']);
    static::assertSame('2025-08-14', $expenses[0]['date']);
    static::assertSame(1.23, $expenses[0]['amount']);
    static::assertSame('test', $expenses[0]['description']);
    static::assertSame($attachments, $expenses[0]['attachments']);

    // If there's more than one expense line only the first one is returned.
    ExpenseLineFixture::addFixture($expense['id'], 4.56);
    $expenses = $this->expenseLoader->getExpensesByCaseId($case['id']);
    static::assertCount(1, $expenses);
    static::assertSame(1.23, $expenses[0]['amount']);
  }

}
