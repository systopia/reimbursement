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

  /**
   * @return array<string, array<string|string[]>>
   */
  public static function permissions(): array {
    return CaseType::permissions();
  }

}
