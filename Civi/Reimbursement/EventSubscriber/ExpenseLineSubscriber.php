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
use Civi\Core\Event\PostEvent;
use Civi\RemoteTools\RequestContext\RequestContextInterface;
use CRM_Reimbursement_ExtensionUtil as E;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ExpenseLineSubscriber implements EventSubscriberInterface {

  private RequestContextInterface $requestContext;

  public static function getSubscribedEvents(): array {
    return [
      'hook_civicrm_post::ExpenseLine' => 'onPostExpenseLine',
    ];
  }

  public function __construct(RequestContextInterface $requestContext) {
    $this->requestContext = $requestContext;
  }

  public function onPostExpenseLine(PostEvent $event): void {
    if ('delete' === $event->action) {
      return;
    }

    $id = $event->id;
    $expenseLine = ExpenseLine::get(FALSE)
      ->setSelect([
        'amount',
        'description',
        'expense_id',
        'expense_id.case_id',
        'expense_id.contact_id',
        'expense_id.type_id',
      ])
      ->addWhere('id', '=', $id)
      ->execute()
      ->single();

    $targetContactIds = [];
    if (NULL !== $expenseLine['expense_id.contact_id']) {
      $targetContactIds[] = $expenseLine['expense_id.contact_id'];
    }

    $createAction = Activity::create(FALSE)
      ->setValues([
        'status_id:name' => 'Completed',
        'case_id' => $expenseLine['expense_id.case_id'],
        'source_contact_id' => $this->getSourceContactId(),
        'target_contact_id' => $targetContactIds,
        'activity_date_time' => \CRM_Utils_Time::date('Y-m-d H:i:s'),
        'reimbursement_expense_line.expense_line_id' => $expenseLine['id'],
        'reimbursement_expense_line.expense_id' => $expenseLine['expense_id'],
        'reimbursement_expense_line.amount' => $expenseLine['amount'],
        'reimbursement_expense_line.description' => $expenseLine['description'],
        'reimbursement_expense_line.expense_type_id' => $expenseLine['expense_id.type_id'],
      ]);

    if ('create' === $event->action) {
      $createAction
        ->addValue('activity_type_id:name', 'expense_line_added')
        ->addValue('subject', E::ts('Expense Line Added (ID: %1)', [1 => $expenseLine['id']]));
    }
    else {
      $createAction
        ->addValue('activity_type_id:name', 'expense_line_updated')
        ->addValue('subject', E::ts('Expense Line Updated (ID: %1)', [1 => $expenseLine['id']]));
    }

    $createAction->execute()->single();
  }

  private function getSourceContactId(): int {
    return 0 === $this->requestContext->getContactId()
      ? (int) \CRM_Core_BAO_Domain::getDomain()->contact_id
      : $this->requestContext->getContactId();
  }

}
