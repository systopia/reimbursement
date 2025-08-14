<?php
/*
 * Copyright (C) 2025 SYSTOPIA GmbH
 *
 *  This program is free software: you can redistribute it and/or modify it under
 *  the terms of the GNU Affero General Public License as published by the Free
 *  Software Foundation, either version 3 of the License, or (at your option) any
 *  later version.
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

namespace Civi\Reimbursement\Form;

use Civi\RemoteTools\Api4\Api4Interface;
use Civi\RemoteTools\Form\FormSpec\DataTransformerInterface;
use CRM_Reimbursement_ExtensionUtil as E;

final class ReimbursementCreateDataTransformer implements DataTransformerInterface {

  private Api4Interface $api4;

  public function __construct(Api4Interface $api4) {
    $this->api4 = $api4;
  }

  /**
   * @throws \CRM_Core_Exception
   */
  public function toEntityValues(array $formData, ?array $currentEntityValues, ?int $contactId): array {
    $formData['contact_id'] = $contactId;
    $formData['creator_id'] = $contactId;

    if (!isset($formData['title'])) {
      // title has to be unique.
      $date = \CRM_Utils_Date::customFormat(\CRM_Utils_Time::date('Y-m-d H:i:s'));
      if (NULL === $contactId) {
        $formData['title'] = E::ts('Reimbursement request on %1', [1 => $date]) . ' (' . uniqid() . ')';
      }
      else {
        $contact = $this->api4->getEntity('Contact', $contactId);
        assert(NULL !== $contact);
        $formData['title'] = E::ts('Reimbursement request by %1 on %2', [1 => $contact['display_name'], 2 => $date]);
      }
    }

    return $formData;
  }

}
