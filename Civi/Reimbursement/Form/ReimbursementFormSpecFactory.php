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

use Civi\Core\SettingsBag;
use Civi\Reimbursement\Helper\ExpenseTypeLoader;
use Civi\Reimbursement\Helper\FieldsLoader;
use Civi\RemoteTools\Api4\Query\Comparison;
use Civi\RemoteTools\Form\FormSpec\Button\SubmitButton;
use Civi\RemoteTools\Form\FormSpec\Field\CalculateField;
use Civi\RemoteTools\Form\FormSpec\Field\FieldCollectionField;
use Civi\RemoteTools\Form\FormSpec\Field\FieldListField;
use Civi\RemoteTools\Form\FormSpec\Field\IntegerField;
use Civi\RemoteTools\Form\FormSpec\FormFieldFactoryInterface;
use Civi\RemoteTools\Form\FormSpec\FormSpec;

use CRM_Reimbursement_ExtensionUtil as E;

final class ReimbursementFormSpecFactory {

  private FieldsLoader $fieldsLoader;

  private ExpenseTypeLoader $expenseTypeLoader;

  private FormFieldFactoryInterface $formFieldFactory;

  private ReimbursementCreateDataTransformer $createDataTransformer;

  private ReimbursementDataTransformer $dataTransformer;

  private SettingsBag $settings;

  public function __construct(
    FieldsLoader $fieldsLoader,
    ExpenseTypeLoader $expenseTypeLoader,
    FormFieldFactoryInterface $formFieldFactory,
    ReimbursementCreateDataTransformer $createDataTransformer,
    ReimbursementDataTransformer $dataTransformer,
    ?SettingsBag $settings = NULL,
  ) {
    $this->fieldsLoader = $fieldsLoader;
    $this->expenseTypeLoader = $expenseTypeLoader;
    $this->formFieldFactory = $formFieldFactory;
    $this->createDataTransformer = $createDataTransformer;
    $this->dataTransformer = $dataTransformer;
    $this->settings = $settings ?? \Civi::settings();
  }

  /**
   * @param array<string, mixed>|null $entityValues
   *
   * @throws \CRM_Core_Exception
   */
  public function createFormSpec(string $caseTypeName, ?array $entityValues): FormSpec {
    $expensesByTypeId = [];
    /** @var array<string, mixed> $expense */
    // @phpstan-ignore foreach.nonIterable
    foreach ($entityValues['expenses'] ?? [] as $expense) {
      $expensesByTypeId[$expense['type_id']][] = $expense;
    }

    $formSpec = new FormSpec(E::ts('Reimbursement'));

    $totalFieldNames = [];
    foreach ($this->expenseTypeLoader->getExpenseTypes() as $typeId => [$typeName, $typeLabel]) {
      $fieldCollectionField = new FieldCollectionField('', '');

      $fieldCollectionField->addField((new IntegerField('id', 'ID'))
        ->setHidden(TRUE));

      $amountField = $this->fieldsLoader->getFields('ExpenseLine', [], Comparison::new('name', '=', 'amount'))[0];
      $fieldCollectionField->addField(
        $this->formFieldFactory->createFormField($amountField, NULL)
      );

      $fields = $this->fieldsLoader->getPublicCustomFields('Expense', ['type_id' => $typeId]);
      foreach ($fields as $field) {
        $fieldCollectionField->addField($this->formFieldFactory->createFormField($field, NULL));
      }

      $formSpec->addElement((new FieldListField("expenses_$typeId", $typeLabel, $fieldCollectionField))
        ->setItemLayout(FieldListField::LAYOUT_VERTICAL)
        ->setAddButtonLabel(E::ts('Add %1', [1 => $typeLabel]))
        ->setRemoveButtonLabel(E::ts('Remove %1', [1 => $typeLabel]))
        ->setDefaultValue($expensesByTypeId[$typeId] ?? [])
      );

      $totalFieldName = "_expenses_{$typeId}_total";
      $totalFieldNames[] = $totalFieldName;
      $formSpec->addElement((new CalculateField(
        $totalFieldName, '', 'sum(map({expenses_' . $typeId . '}, "value.amount ?: 0"))'
        ))
        ->setDefaultValue(0)
        ->setHidden(TRUE)
      );
    }

    $caseFields = $this->fieldsLoader->getPublicCustomFields('Case', ['case_type_id.name' => $caseTypeName]);
    foreach ($caseFields as $caseField) {
      $formSpec->addElement($this->formFieldFactory->createFormField($caseField, $entityValues));
    }

    $formSpec->addElement(
      (new CalculateField(
        '_total',
        E::ts('Total in %1', [1 => $this->settings->get('defaultCurrency')]),
        '{' . implode('} + {', $totalFieldNames) . '}')
      )->setDefaultValue(0)
    );

    $formSpec->addElement(new SubmitButton('_action', 'submit', E::ts('Submit')));

    if (!isset($entityValues['id'])) {
      $formSpec->appendDataTransformer($this->createDataTransformer);
    }
    $formSpec->appendDataTransformer($this->dataTransformer);

    return $formSpec;
  }

}
