<?php
declare(strict_types = 1);

namespace Civi\Api4;

/**
 * ReimbursementCaseTypeConfig entity.
 *
 * Provided by the reimbursement extension.
 *
 * @package Civi\Api4
 */
final class ReimbursementCaseTypeConfig extends Generic\DAOEntity {

  public static function permissions() {
    return CaseType::permissions();
  }

}
