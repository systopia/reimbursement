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

use CRM_Reimbursement_ExtensionUtil as E;

/**
 * Collection of upgrade steps.
 */
final class CRM_Reimbursement_Upgrader extends \CRM_Extension_Upgrader_Base {

  /**
   * Implements hook_civicrm_upgrade_N().
   *
   * Add the following columns to civicrm_reimbursement_case_type_config
   * - expense_add_label
   * - expense_remove_label
   * - attachment_add_label
   * - attachment_remove_label
   */
  public function upgrade_0001(): bool {
    $this->ctx->log->info('Add expense_add_label column to civicrm_reimbursement_case_type_config');
    E::schema()->alterSchemaField('ReimbursementCaseTypeConfig', 'expense_add_label', [
      'title' => E::ts('Expense Add Label'),
      'sql_type' => 'varchar(100)',
      'input_type' => 'Text',
      'required' => FALSE,
      'description' => E::ts('This label, if given, will override the default one.'),
      'default' => FALSE,
    ]);

    $this->ctx->log->info('Add expense_remove_label column to civicrm_reimbursement_case_type_config');
    E::schema()->alterSchemaField('ReimbursementCaseTypeConfig', 'expense_remove_label', [
      'title' => E::ts('Expense Remove Label'),
      'sql_type' => 'varchar(100)',
      'input_type' => 'Text',
      'required' => FALSE,
      'description' => E::ts('This label, if given, will override the default one.'),
      'default' => FALSE,
    ]);

    $this->ctx->log->info('Add attachment_add_label column to civicrm_reimbursement_case_type_config');
    E::schema()->alterSchemaField('ReimbursementCaseTypeConfig', 'attachment_add_label', [
      'title' => E::ts('Attachment Add Label'),
      'sql_type' => 'varchar(100)',
      'input_type' => 'Text',
      'required' => FALSE,
      'description' => E::ts('This label, if given, will override the default one.'),
      'default' => FALSE,
    ]);

    $this->ctx->log->info('Add attachment_remove_label column to civicrm_reimbursement_case_type_config');
    E::schema()->alterSchemaField('ReimbursementCaseTypeConfig', 'attachment_remove_label', [
      'title' => E::ts('Attachment Remove Label'),
      'sql_type' => 'varchar(100)',
      'input_type' => 'Text',
      'required' => FALSE,
      'description' => E::ts('This label, if given, will override the default one.'),
      'default' => FALSE,
    ]);

    return TRUE;
  }

}
