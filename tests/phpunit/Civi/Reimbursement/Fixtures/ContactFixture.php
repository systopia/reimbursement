<?php
/*
 * Copyright (C) 2022 SYSTOPIA GmbH
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

namespace Civi\Reimbursement\Fixtures;

use Civi\Api4\Contact;

final class ContactFixture {

  /**
   * @param array<string, mixed> $values
   *
   * @return array<string, mixed>
   *
   * @throws \CRM_Core_Exception
   */
  public static function addIndividualFixture(array $values = []): array {
    return Contact::create(FALSE)
      ->setValues($values + [
        'contact_type' => 'Individual',
        'first_name' => 'Some',
        'last_name' => 'Individual',
      ])->execute()->single();
  }

  /**
   * @param array<string, mixed> $values
   *
   * @return array<string, mixed>
   *
   * @throws \CRM_Core_Exception
   */
  public static function addOrganizationFixture(array $values = []): array {
    $values += [
      'contact_type' => 'Organization',
      'legal_name' => 'Test organization',
    ];
    $values['organization_name'] ??= $values['legal_name'];
    $values['display_name'] ??= $values['organization_name'];

    return Contact::create(FALSE)
      ->setValues($values)->execute()->single();
  }

}
