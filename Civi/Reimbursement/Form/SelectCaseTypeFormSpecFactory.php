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

use Civi\RemoteTools\Form\FormSpec\Button\SubmitButton;
use Civi\RemoteTools\Form\FormSpec\Field\SelectField;
use Civi\RemoteTools\Form\FormSpec\FormSpec;
use Civi\RemoteTools\Form\FormSpec\ValidatorInterface;
use Civi\RemoteTools\Form\Validation\ValidationError;
use Civi\RemoteTools\Form\Validation\ValidationResult;
use CRM_Reimbursement_ExtensionUtil as E;

final class SelectCaseTypeFormSpecFactory {

  /**
   * @param list<\Civi\Reimbursement\CaseTypeConfigData> $caseTypeConfigs
   */
  public function createFormSpec(array $caseTypeConfigs): FormSpec {
    $options = [];
    foreach ($caseTypeConfigs as $config) {
      $options[$config->getCaseTypeName()] = $config->getCaseTypeLabel();
    }
    $typeField = new SelectField('type', E::ts('Type'), $options);
    $typeField->setRequired(TRUE);

    return (new FormSpec(E::ts('Reimbursement')))
      ->addElement($typeField)
      ->addElement(new SubmitButton('_action', 'submit', E::ts('Next')))
      ->setSubmitMethod('GET')
      ->appendValidator(new class implements ValidatorInterface {

        public function validate(
          array $formData,
          ?array $currentEntityValues,
          ?int $contactId
        ): ValidationResult {
          // This is never executed under normal conditions because the submit method is "GET".
          return ValidationResult::new(ValidationError::new('', 'Submitting this form is not allowed'));
        }

      });
  }

}
