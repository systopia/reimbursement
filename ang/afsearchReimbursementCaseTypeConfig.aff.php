<?php
use CRM_Reimbursement_ExtensionUtil as E;

return [
  'type' => 'search',
  'title' => E::ts('Reimbursement Case Type Configs'),
  'icon' => 'fa-list-alt',
  'server_route' => 'civicrm/admin/reimbursement/case-type-config',
  'permission' => [
    'administer CiviCRM',
  ],
];
