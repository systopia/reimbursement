<?php
use CRM_Reimbursement_ExtensionUtil as E;

return [
  'type' => 'form',
  'title' => E::ts('Reimbursement Case Type Config'),
  'icon' => 'fa-list-alt',
  'server_route' => 'civicrm/admin/reimbursement/case-type-config/edit',
  'permission' => [
    'administer CiviCRM',
  ],
  'create_submission' => TRUE,
];
