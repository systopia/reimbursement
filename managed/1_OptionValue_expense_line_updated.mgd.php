<?php
use CRM_Reimbursement_ExtensionUtil as E;

return [
  [
    'name' => 'OptionValue_expense_line_updated',
    'entity' => 'OptionValue',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'activity_type',
        'label' => E::ts('Expense Line Updated'),
        'name' => 'expense_line_updated',
      ],
      'match' => [
        'option_group_id',
        'name',
        'value',
      ],
    ],
  ],
];
