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

namespace Civi\Reimbursement\EventSubscriber;

use Civi\Api4\Activity;
use Civi\Api4\ExpenseLine;
use Civi\Reimbursement\AbstractReimbursementHeadlessTestCase;
use Civi\Reimbursement\Fixtures\CaseFixture;
use Civi\Reimbursement\Fixtures\ContactFixture;
use Civi\Reimbursement\Fixtures\ExpenseFixture;
use Civi\Reimbursement\Fixtures\ExpenseLineFixture;
use Civi\Reimbursement\Fixtures\ExpenseTypeFixture;

/**
 * @covers \Civi\Reimbursement\EventSubscriber\ExpenseLineSubscriber
 *
 * @group headless
 */
final class ExpenseLineSubscriberTest extends AbstractReimbursementHeadlessTestCase {

  protected function setUp(): void {
    parent::setUp();
  }

  public function test(): void {
    $contact = ContactFixture::addIndividualFixture();
    $case = CaseFixture::addFixture($contact['id']);
    ExpenseTypeFixture::addFixture(999, 'test');
    $expense = ExpenseFixture::addFixture($case['id'], $contact['id'], 'test', ['date' => '2025-08-14']);
    $expenseLine = ExpenseLineFixture::addFixture($expense['id'], 123, ['description' => 'test']);

    $addActivity = Activity::get(FALSE)
      ->addSelect('*', 'custom.*', 'activity_type_id:name', 'status_id:name', 'case_id')
      ->addWhere('reimbursement_expense_line.expense_line_id', '=', $expenseLine['id'])
      ->execute()
      ->single();

    static::assertSame('expense_line_added', $addActivity['activity_type_id:name']);
    static::assertSame('Completed', $addActivity['status_id:name']);
    static::assertSame($case['id'], $addActivity['case_id']);
    // @phpstan-ignore encapsedStringPart.nonString
    static::assertSame("Expense Line Added (ID: {$expenseLine['id']})", $addActivity['subject']);
    static::assertSame($expense['id'], $addActivity['reimbursement_expense_line.expense_id']);
    static::assertSame($expenseLine['id'], $addActivity['reimbursement_expense_line.expense_line_id']);
    static::assertSame(123.0, $addActivity['reimbursement_expense_line.amount']);
    static::assertSame('test', $addActivity['reimbursement_expense_line.description']);
    static::assertSame(999, $addActivity['reimbursement_expense_line.expense_type_id']);

    ExpenseLine::update(FALSE)
      ->setValues(['amount' => 456, 'description' => 'changed'])
      ->addWhere('id', '=', $expenseLine['id'])
      ->execute();

    $updateActivity = Activity::get(FALSE)
      ->addSelect('*', 'custom.*', 'activity_type_id:name', 'status_id:name', 'case_id')
      ->addWhere('reimbursement_expense_line.expense_line_id', '=', $expenseLine['id'])
      ->addWhere('id', '!=', $addActivity['id'])
      ->execute()
      ->single();

    static::assertSame('expense_line_updated', $updateActivity['activity_type_id:name']);
    static::assertSame('Completed', $updateActivity['status_id:name']);
    static::assertSame($case['id'], $updateActivity['case_id']);
    // @phpstan-ignore encapsedStringPart.nonString
    static::assertSame("Expense Line Updated (ID: {$expenseLine['id']})", $updateActivity['subject']);
    static::assertSame($expense['id'], $updateActivity['reimbursement_expense_line.expense_id']);
    static::assertSame($expenseLine['id'], $updateActivity['reimbursement_expense_line.expense_line_id']);
    static::assertSame(456.0, $updateActivity['reimbursement_expense_line.amount']);
    static::assertSame('changed', $updateActivity['reimbursement_expense_line.description']);
    static::assertSame(999, $updateActivity['reimbursement_expense_line.expense_type_id']);
  }

}
