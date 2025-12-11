<?php
/*
 * Copyright (C) 2023 SYSTOPIA GmbH
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation in version 3.
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

namespace Civi\Reimbursement\Helper;

use Civi\RemoteTools\Api4\Api4Interface;

/**
 * @phpstan-import-type fieldT from \Civi\RemoteTools\Form\FormSpec\FormFieldFactoryInterface
 */
final class CustomFieldsHelper {

  private Api4Interface $api4;

  public function __construct(Api4Interface $api4) {
    $this->api4 = $api4;
  }

  /**
   * @phpstan-param array<fieldT> $entityFields
   *   Field spec of fields returned by getFields action.
   *
   * @phpstan-return array<string, fieldT>
   *   Public custom fields in $entityFields ordered by their custom field
   *   weight ascending. Key is the field name. The "required" flag is set to
   *   the value of CustomField.is_required.
   */
  public function getPublicFieldsOrderedByWeight(array $entityFields): array {
    $fieldsByCustomFieldId = array_column($entityFields, NULL, 'custom_field_id');

    /** @var iterable<array{id: int, is_required: bool}> $customFields */
    $customFields = $this->api4->execute('CustomField', 'get', [
      'select' => ['id', 'is_required'],
      'where' => [
        ['id', 'IN', array_keys($fieldsByCustomFieldId)],
        ['custom_group_id.is_public', '=', TRUE],
      ],
      'orderBy' => [
        'custom_group_id.weight' => 'ASC',
        'weight' => 'ASC',
      ],
    ]);

    $orderedFields = [];
    foreach ($customFields as $customField) {
      $fieldsByCustomFieldId[$customField['id']]['required'] = $customField['is_required'];
      /** @phpstan-var fieldT $field */
      $field = $fieldsByCustomFieldId[$customField['id']];
      $orderedFields[$field['name']] = $field;
    }

    return $orderedFields;
  }

}
