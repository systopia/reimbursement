<?php
declare(strict_types = 1);

use Civi\Reimbursement\Form\ExpensesPlacement;
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
        'maxlength' => 40,
        'placeholder' => ts('Custom label'),
      ],
    ],
    'submit_button_label' => [
      'title' => E::ts('Submit Button Label'),
      'sql_type' => 'varchar(40)',
      'input_type' => 'Text',
      'input_attrs' => [
        'maxlength' => 40,
        'placeholder' => ts('Custom label'),
      ],
    ],
    'subject_field_enabled' => [
      'title' => E::ts('Subject Field Enabled'),
      'description' => E::ts('Should the Case field "Subject" be shown in the form?'),
      'sql_type' => 'tinyint(1)',
      'input_type' => 'CheckBox',
      'data_type' => 'Boolean',
      'required' => TRUE,
      'default' => FALSE,
    ],
    'subject_field_label' => [
      'title' => E::ts('Subject Field Label'),
      'description' => E::ts('This label, if given, will override the default one.'),
      'sql_type' => 'varchar(100)',
      'input_type' => 'Text',
      'data_type' => 'String',
      'input_attrs' => [
        'maxlength' => 100,
        'placeholder' => ts('Custom label'),
      ],
    ],
    'subject_field_description' => [
      'title' => E::ts('Subject Field Description'),
      'sql_type' => 'varchar(255)',
      'input_type' => 'TextArea',
      'data_type' => 'String',
      'input_attrs' => [
        'maxlength' => 255,
      ],
    ],
    'details_field_enabled' => [
      'title' => E::ts('Details Field Enabled'),
      'description' => E::ts('Should the Case field "Details" be shown in the form?'),
      'sql_type' => 'tinyint(1)',
      'input_type' => 'CheckBox',
      'data_type' => 'Boolean',
      'required' => TRUE,
      'default' => FALSE,
    ],
    'details_field_label' => [
      'title' => E::ts('Details Field Label'),
      'description' => E::ts('This label, if given, will override the default one.'),
      'sql_type' => 'varchar(100)',
      'input_type' => 'Text',
      'data_type' => 'String',
      'input_attrs' => [
        'maxlength' => 100,
        'placeholder' => ts('Custom label'),
      ],
    ],
    'details_field_description' => [
      'title' => E::ts('Details Field Description'),
      'sql_type' => 'varchar(255)',
      'input_type' => 'TextArea',
      'data_type' => 'String',
      'input_attrs' => [
        'maxlength' => 255,
      ],
    ],
    'start_date_field_enabled' => [
      'title' => E::ts('Start Date Field Enabled'),
      'description' => E::ts('Should the Case field "Start Date" be shown in the form?'),
      'sql_type' => 'tinyint(1)',
      'input_type' => 'CheckBox',
      'data_type' => 'Boolean',
      'required' => TRUE,
      'default' => FALSE,
    ],
    'start_date_field_label' => [
      'title' => E::ts('Start Date Field Label'),
      'description' => E::ts('This label, if given, will override the default one.'),
      'sql_type' => 'varchar(100)',
      'input_type' => 'Text',
      'data_type' => 'String',
      'input_attrs' => [
        'maxlength' => 100,
        'placeholder' => ts('Custom label'),
      ],
    ],
    'start_date_field_description' => [
      'title' => E::ts('Start Date Field Description'),
      'sql_type' => 'varchar(255)',
      'input_type' => 'TextArea',
      'data_type' => 'String',
      'input_attrs' => [
        'maxlength' => 255,
      ],
    ],
    'end_date_field_enabled' => [
      'title' => E::ts('End Date Field Enabled'),
      'description' => E::ts('Should the Case field "End Date" be shown in the form?'),
      'sql_type' => 'tinyint(1)',
      'input_type' => 'CheckBox',
      'data_type' => 'Boolean',
      'required' => TRUE,
      'default' => FALSE,
    ],
    'end_date_field_label' => [
      'title' => E::ts('End Date Field Label'),
      'description' => E::ts('This label, if given, will override the default one.'),
      'sql_type' => 'varchar(100)',
      'input_type' => 'Text',
      'data_type' => 'String',
      'input_attrs' => [
        'maxlength' => 100,
        'placeholder' => ts('Custom label'),
      ],
    ],
    'end_date_field_description' => [
      'title' => E::ts('End Date Field Description'),
      'sql_type' => 'varchar(255)',
      'input_type' => 'TextArea',
      'data_type' => 'String',
      'input_attrs' => [
        'maxlength' => 255,
      ],
    ],
    'expenses_placement' => [
      'title' => E::ts('Placement of Expenses'),
      'sql_type' => 'varchar(40)',
      'input_type' => 'Select',
      'required' => TRUE,
      'pseudoconstant' => [
        'callback' => fn () => [...ExpensesPlacement::labels()],
      ],
    ],
    'expense_add_label' => [
      'title' => E::ts('Expense Add Label'),
      'description' => E::ts('This label, if given, will override the default one.'),
      'sql_type' => 'varchar(100)',
      'input_type' => 'Text',
      'data_type' => 'String',
      'input_attrs' => [
        'maxlength' => 100,
        'placeholder' => ts('Custom label'),
      ],
    ],
    'expense_remove_label' => [
      'title' => E::ts('Expense Remove Label'),
      'description' => E::ts('This label, if given, will override the default one.'),
      'sql_type' => 'varchar(100)',
      'input_type' => 'Text',
      'data_type' => 'String',
      'input_attrs' => [
        'maxlength' => 100,
        'placeholder' => ts('Custom label'),
      ],
    ],
    'attachment_add_label' => [
      'title' => E::ts('Attachment Add Label'),
      'description' => E::ts('This label, if given, will override the default one.'),
      'sql_type' => 'varchar(100)',
      'input_type' => 'Text',
      'data_type' => 'String',
      'input_attrs' => [
        'maxlength' => 100,
        'placeholder' => ts('Custom label'),
      ],
    ],
    'attachment_remove_label' => [
      'title' => E::ts('Attachment Remove Label'),
      'description' => E::ts('This label, if given, will override the default one.'),
      'sql_type' => 'varchar(100)',
      'input_type' => 'Text',
      'data_type' => 'String',
      'input_attrs' => [
        'maxlength' => 100,
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
