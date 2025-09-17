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
use Civi\Reimbursement\CaseTypeConfigData;
use Civi\Reimbursement\Helper\ExpenseTypeLoader;
use Civi\Reimbursement\Helper\FieldsLoader;
use Civi\RemoteTools\Form\FormSpec\Button\SubmitButton;
use Civi\RemoteTools\Form\FormSpec\Field\AttachmentsField;
use Civi\RemoteTools\Form\FormSpec\Field\CalculateField;
use Civi\RemoteTools\Form\FormSpec\Field\FieldCollectionField;
use Civi\RemoteTools\Form\FormSpec\Field\FieldListField;
use Civi\RemoteTools\Form\FormSpec\Field\IntegerField;
use Civi\RemoteTools\Form\FormSpec\FormFieldFactoryInterface;
use Civi\RemoteTools\Form\FormSpec\FormSpec;
use CRM_Reimbursement_ExtensionUtil as E;

/**
 * @phpstan-import-type fieldT from FormFieldFactoryInterface
 */
final class ReimbursementFormSpecFactory {

  private FieldsLoader $fieldsLoader;

  private ExpenseTypeLoader $expenseTypeLoader;

  private FormFieldFactoryInterface $formFieldFactory;

  private ReimbursementCreateDataTransformerFactory $createDataTransformerFactory;

  private ReimbursementDataTransformerFactory $dataTransformerFactory;

  private SettingsBag $settings;

  public function __construct(
    FieldsLoader $fieldsLoader,
    ExpenseTypeLoader $expenseTypeLoader,
    FormFieldFactoryInterface $formFieldFactory,
    ReimbursementCreateDataTransformerFactory $createDataTransformerFactory,
    ReimbursementDataTransformerFactory $dataTransformerFactory,
    ?SettingsBag $settings = NULL,
  ) {
    $this->fieldsLoader = $fieldsLoader;
    $this->expenseTypeLoader = $expenseTypeLoader;
    $this->formFieldFactory = $formFieldFactory;
    $this->createDataTransformerFactory = $createDataTransformerFactory;
    $this->dataTransformerFactory = $dataTransformerFactory;
    $this->settings = $settings ?? \Civi::settings();
  }

  /**
   * @param array<string, mixed>|null $entityValues
   *
   * @throws \CRM_Core_Exception
   *
   * phpcs:disable Generic.Metrics.CyclomaticComplexity.TooHigh
   */
  public function createFormSpec(CaseTypeConfigData $config, ?array $entityValues): FormSpec {
  // phpcs:enable
    $readOnly = isset($entityValues['id'])
      && !in_array($entityValues['status_id'], $config->getWritableCaseStatusIds(), TRUE);

    $expensesByTypeId = [];
    /** @var array<string, mixed> $expense */
    // @phpstan-ignore foreach.nonIterable
    foreach ($entityValues['expenses'] ?? [] as $expense) {
      $expensesByTypeId[$expense['type_id']][] = $expense;
    }

    $formSpec = new FormSpec(E::ts('Reimbursement'));

    $amountField = $this->fieldsLoader->getField('ExpenseLine', 'amount');
    $descriptionField = $this->fieldsLoader->getField('ExpenseLine', 'description');
    $totalFieldNames = [];
    $expenseTypes = $this->expenseTypeLoader->getExpenseTypes($config->getExpenseTypeIds());
    foreach ($expenseTypes as $typeId => [$typeName, $typeLabel]) {
      $formSpec->addElement($this->createExpenseTypeField($typeId, $typeLabel, $amountField, $descriptionField)
        ->setDefaultValue($expensesByTypeId[$typeId] ?? [])
        ->setReadOnly($readOnly)
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

    $caseFields = $this->fieldsLoader->getPublicCustomFields('Case', ['case_type_id' => $config->getCaseTypeId()]);
    foreach ($caseFields as $caseField) {
      $formSpec->addElement(
        $this->formFieldFactory->createFormField($caseField, $entityValues)->setReadOnly($readOnly)
      );
    }

    $formSpec->addElement(
      (new CalculateField(
        '_total',
        E::ts('Total in %1', [1 => $this->settings->get('defaultCurrency')]),
        '{' . implode('} + {', $totalFieldNames) . '}')
      )->setDefaultValue(0)
    );

    if (!$readOnly) {
      $formSpec->addElement(new SubmitButton('_action', 'save', $config->getSaveButtonLabel() ?? E::ts('Save')));
      if (NULL !== $config->getSubmitCaseStatusId()) {
        $formSpec->addElement(
          new SubmitButton('_action', 'submit', $config->getSubmitButtonLabel() ?? E::ts('Submit'))
        );
      }
    }

    if (!isset($entityValues['id'])) {
      $formSpec->appendDataTransformer($this->createDataTransformerFactory->createTransformer($config));
    }
    $formSpec->appendDataTransformer($this->dataTransformerFactory->createTransformer($config));

    return $formSpec;
  }

  /**
   * @phpstan-param fieldT $amountField
   * @phpstan-param fieldT $descriptionField
   *
   * @throws \CRM_Core_Exception
   */
  private function createExpenseTypeField(
    int $typeId,
    string $typeLabel,
    array $amountField,
    array $descriptionField
  ): FieldListField {
    $fieldCollectionField = (new FieldCollectionField('', ''));

    $fieldCollectionField->addField((new IntegerField('id', 'ID'))
      ->setHidden(TRUE)
      ->setReadOnly(TRUE)
    );

    $fieldCollectionField->addField(
      // @phpstan-ignore method.notFound
      $this->formFieldFactory->createFormField($amountField, NULL)->setMinimum(0)
    );
    $fieldCollectionField->addField($this->formFieldFactory->createFormField($descriptionField, NULL));
    $fieldCollectionField->addField(new AttachmentsField('attachments', E::ts('Attachments')));

    $fields = $this->fieldsLoader->getPublicCustomFields('Expense', ['type_id' => $typeId]);
    foreach ($fields as $field) {
      $fieldCollectionField->addField($this->formFieldFactory->createFormField($field, NULL));
    }

    return (new FieldListField("expenses_$typeId", $typeLabel, $fieldCollectionField))
      ->setItemLayout(FieldListField::LAYOUT_VERTICAL)
      ->setAddButtonLabel(E::ts('Add %1', [1 => $typeLabel]))
      ->setRemoveButtonLabel(E::ts('Remove %1', [1 => $typeLabel]));
  }

}
