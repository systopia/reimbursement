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

namespace Civi\Reimbursement\Form;

use Civi\Api4\CaseType;
use Civi\Reimbursement\AbstractReimbursementHeadlessTestCase;
use Civi\Reimbursement\CaseTypeConfigData;
use Civi\Reimbursement\Fixtures\ExpenseTypeFixture;
use Civi\RemoteTools\Form\FormSpec\Field\AttachmentsField;
use Civi\RemoteTools\Form\FormSpec\Field\CalculateField;
use Civi\RemoteTools\Form\FormSpec\Field\FieldCollectionField;
use Civi\RemoteTools\Form\FormSpec\Field\FieldListField;
use Civi\RemoteTools\Form\FormSpec\Field\IntegerField;
use Civi\RemoteTools\Form\FormSpec\Field\MoneyField;
use Civi\RemoteTools\Form\FormSpec\Field\TextField;

/**
 * @covers \Civi\Reimbursement\Form\ReimbursementFormSpecFactory
 *
 * @group headless
 */
final class ReimbursementFormSpecFactoryTest extends AbstractReimbursementHeadlessTestCase {

  private ReimbursementFormSpecFactory $formSpecFactory;

  protected function setUp(): void {
    parent::setUp();
    // @phpstan-ignore assign.propertyType
    $this->formSpecFactory = \Civi::service(ReimbursementFormSpecFactory::class . 'Alias');
  }

  public function testNewSimple(): void {
    $caseType = CaseType::get(FALSE)->addWhere('name', '=', 'reimbursement')->execute()->single();
    ExpenseTypeFixture::addFixture(333, 'ExpenseType');

    $caseTypeConfig = new CaseTypeConfigData([
      'id' => 111,
      'case_type_id' => $caseType['id'],
      'case_type_id:name' => 'reimbursement',
      'case_type_id:label' => 'Test',
      'initial_case_status_id' => 222,
      'submit_case_status_id' => NULL,
      'writable_case_status_ids' => [],
      'expense_type_ids' => [333],
      'expense_status_id' => 444,
      'save_button_label' => 'Test Save',
      'submit_button_label' => NULL,
    ]);

    $formSpec = $this->formSpecFactory->createFormSpec($caseTypeConfig, NULL);
    static::assertSame('Reimbursement', $formSpec->getTitle());

    $submitButtons = $formSpec->getSubmitButtons();
    static::assertSame(['_action'], array_keys($submitButtons));
    static::assertCount(1, $submitButtons['_action']);
    static::assertSame('Test Save', $submitButtons['_action'][0]->getLabel());

    $fields = $formSpec->getFields();
    static::assertCount(3, $fields);
    static::assertSame('ExpenseType', $fields['expenses_333']->getLabel());
    static::assertInstanceOf(FieldListField::class, $fields['expenses_333']);
    static::assertFalse($fields['expenses_333']->isReadOnly());

    $itemField = $fields['expenses_333']->getItemField();
    static::assertInstanceOf(FieldCollectionField::class, $itemField);
    static::assertCount(4, $itemField->getFields());
    static::assertInstanceOf(IntegerField::class, $itemField->getFields()[0]);
    static::assertSame('id', $itemField->getFields()[0]->getName());
    static::assertTrue($itemField->getFields()[0]->isHidden());
    static::assertTrue($itemField->getFields()[0]->isReadOnly());
    static::assertInstanceOf(MoneyField::class, $itemField->getFields()[1]);
    static::assertSame('amount', $itemField->getFields()[1]->getName());
    static::assertSame('Amount in USD', $itemField->getFields()[1]->getLabel());
    static::assertSame('USD', $itemField->getFields()[1]->getCurrency());
    static::assertSame(0, $itemField->getFields()[1]->getMinimum());
    static::assertInstanceOf(TextField::class, $itemField->getFields()[2]);
    static::assertSame('description', $itemField->getFields()[2]->getName());
    static::assertSame('Description', $itemField->getFields()[2]->getLabel());
    static::assertInstanceOf(AttachmentsField::class, $itemField->getFields()[3]);
    static::assertSame('attachments', $itemField->getFields()[3]->getName());
    static::assertSame('Attachments', $itemField->getFields()[3]->getLabel());

    static::assertInstanceOf(CalculateField::class, $fields['_expenses_333_total']);
    static::assertTrue($fields['_expenses_333_total']->isHidden());
    static::assertInstanceOf(CalculateField::class, $fields['_total']);
    static::assertSame('Total in USD', $fields['_total']->getLabel());
  }

}
