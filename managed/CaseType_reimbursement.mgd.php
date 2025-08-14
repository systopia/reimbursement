<?php
use CRM_Reimbursement_ExtensionUtil as E;

return [
  [
    'name' => 'CaseType_reimbursement',
    'entity' => 'CaseType',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'reimbursement',
        'title' => E::ts('Reimbursement'),
        'description' => E::ts('Reimbursement request.'),
        'definition' => [
          'restrictActivityAsgmtToCmsUser' => 0,
          'activityAsgmtGrps' => [],
          'activityTypes' => [
            [
              'name' => 'Open Case',
              'max_instances' => '1',
            ],
            [
              'name' => 'Email',
            ],
            [
              'name' => 'Follow up',
            ],
            [
              'name' => 'Meeting',
            ],
            [
              'name' => 'Phone Call',
            ],
          ],
          'activitySets' => [
            [
              'name' => 'standard_timeline',
              'label' => E::ts('Standard Timeline'),
              'timeline' => 1,
              'activityTypes' => [
                [
                  'name' => 'Open Case',
                  'status' => 'Completed',
                  'label' => E::ts('Open Case'),
                  'default_assignee_type' => '1',
                ],
              ],
            ],
          ],
          'timelineActivityTypes' => [
            [
              'name' => 'Open Case',
              'status' => 'Completed',
              'label' => E::ts('Open Case'),
              'default_assignee_type' => '1',
            ],
          ],
          'caseRoles' => [
            [
              'name' => 'Case Coordinator',
              'creator' => '0',
              'manager' => '1',
            ],
          ],
        ],
      ],
      'match' => ['name'],
    ],
  ],
];
