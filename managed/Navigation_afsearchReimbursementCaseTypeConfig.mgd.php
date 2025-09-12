<?php
use CRM_Reimbursement_ExtensionUtil as E;

return [
  [
    'name' => 'Navigation_afsearchReimbursementCaseTypeConfig',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Reimbursement Case Type Configs'),
        'name' => 'afsearchReimbursementCaseTypeConfig',
        'url' => 'civicrm/admin/reimbursement/case-type-config',
        'icon' => 'crm-i fa-list-alt',
        'permission' => [
          'administer CiviCRM',
        ],
        'permission_operator' => 'AND',
        'parent_id.name' => 'System Settings',
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
];
