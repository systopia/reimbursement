<?php
declare(strict_types = 1);

use CRM_Reimbursement_ExtensionUtil as E;

return [
  'name' => 'ReimbursementCaseTypeConfig',
  'table' => 'civicrm_reimbursement_case_type_config',
  'class' => 'CRM_Reimbursement_DAO_ReimbursementCaseTypeConfig',
  'getInfo' => fn() => [
    'title' => E::ts('Reimbursement Case Type Config'),
    'title_plural' => E::ts('Reimbursement Case Type Configs'),
    'description' => E::ts('Case type specific configuration for reimbursements.'),
    'log' => TRUE,
  ],
  'getFields' => fn() => [
    'id' => [
      'title' => E::ts('ID'),
      'sql_type' => 'int unsigned',
      'input_type' => 'Number',
      'required' => TRUE,
      'description' => E::ts('Unique ReimbursementCaseTypeConfig ID'),
      'primary_key' => TRUE,
      'auto_increment' => TRUE,
    ],
    'case_type_id' => [
      'title' => E::ts('Case Type ID'),
      'sql_type' => 'int unsigned',
      'input_type' => 'Select',
      'required' => TRUE,
      'input_attrs' => [
        'label' => ts('Case Type'),
      ],
      'pseudoconstant' => [
        'table' => 'civicrm_case_type',
        'key_column' => 'id',
        'label_column' => 'title',
      ],
      'entity_reference' => [
        'entity' => 'CaseType',
        'key' => 'id',
      ],
    ],
    'initial_case_status_id' => [
      'title' => E::ts('Initial Case Status ID'),
      'sql_type' => 'int unsigned',
      'input_type' => 'Select',
      'input_attrs' => [
        'control_field' => 'case_type_id',
        'label' => ts('Initial Case Status'),
      ],
      'pseudoconstant' => [
        'option_group_name' => 'case_status',
        'condition_provider' => [CRM_Case_BAO_Case::class, 'alterStatusOptions'],
      ],
    ],
    'submit_case_status_id' => [
      'title' => E::ts('Submit Case Status ID'),
      'sql_type' => 'int unsigned',
      'input_type' => 'Select',
      'input_attrs' => [
        'control_field' => 'case_type_id',
        'label' => ts('Submit Case Status'),
      ],
      'pseudoconstant' => [
        'option_group_name' => 'case_status',
        'condition_provider' => [CRM_Case_BAO_Case::class, 'alterStatusOptions'],
      ],
    ],
    'writable_case_status_ids' => [
      'title' => E::ts('Writable Case Status IDs'),
      'sql_type' => 'varchar(255)',
      'serialize' => CRM_Core_DAO::SERIALIZE_JSON,
      'input_type' => 'Select',
      'input_attrs' => [
        'control_field' => 'case_type_id',
        'label' => ts('Writable Case Status'),
      ],
      'pseudoconstant' => [
        'option_group_name' => 'case_status',
        'condition_provider' => [CRM_Case_BAO_Case::class, 'alterStatusOptions'],
      ],
    ],
    'expense_type_ids' => [
      'title' => E::ts('Expense Type IDs'),
      'sql_type' => 'varchar(255)',
      'serialize' => CRM_Core_DAO::SERIALIZE_JSON,
      'required' => TRUE,
      'input_type' => 'Select',
      'input_attrs' => [
        'label' => ts('Available Expense Types'),
      ],
      'pseudoconstant' => [
        'option_group_name' => 'expense_type',
      ],
    ],
    'expense_status_id' => [
      'title' => E::ts('Expense Status ID'),
      'description' => E::ts('Expense status on reimbursement form submit.'),
      'required' => TRUE,
      'sql_type' => 'int unsigned',
      'input_type' => 'Select',
      'input_attrs' => [
        'label' => ts('Expense Status'),
      ],
      'pseudoconstant' => [
        'option_group_name' => 'expense_status',
      ],
    ],
    'save_button_label' => [
      'title' => E::ts('Save Button Label'),
      'sql_type' => 'varchar(40)',
      'input_type' => 'Text',
      'input_attrs' => [
        'placeholder' => ts('Custom label'),
      ],
    ],
    'submit_button_label' => [
      'title' => E::ts('Submit Button Label'),
      'sql_type' => 'varchar(40)',
      'input_type' => 'Text',
      'input_attrs' => [
        'placeholder' => ts('Custom label'),
      ],
    ],
  ],
  'getIndices' => fn() => [
    'UI_case_type_id' => [
      'fields' => [
        'case_type_id' => TRUE,
      ],
      'unique' => TRUE,
    ],
  ],
  'getPaths' => fn() => [
    'add' => 'civicrm/admin/reimbursement/case-type-config/edit?action=add&reset=1',
    'update' => 'civicrm/admin/reimbursement/case-type-config/edit#?ReimbursementCaseTypeConfig1=[id]',
    'browse' => 'civicrm/admin/reimbursement/case-type-config?action=browse&reset=1',
  ],
];
