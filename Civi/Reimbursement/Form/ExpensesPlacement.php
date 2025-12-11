<?php
/*
 * Copyright (C) 2025 SYSTOPIA GmbH
 *
 * This program is free software: you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License as published by the Free
 * Software Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types = 1);

namespace Civi\Reimbursement\Form;

use CRM_Reimbursement_ExtensionUtil as E;

enum ExpensesPlacement: string {

  case AboveCaseFields = 'above_case_fields';

  case BelowCaseFields = 'below_case_fields';

  /**
   * @return iterable<string, string>
   *   Mapping of value to label.
   */
  public static function labels(): iterable {
    foreach (self::cases() as $placement) {
      yield $placement->value => $placement->label();
    }
  }

  public function label(): string {
    return match ($this) {
      // https://github.com/voku/phpstan-rules/issues/42
      // @phpstan-ignore voku.Match
      self::AboveCaseFields => E::ts('Above Case Fields'),
      // @phpstan-ignore voku.Match
      self::BelowCaseFields => E::ts('Below Case Fields'),
    };
  }

}
