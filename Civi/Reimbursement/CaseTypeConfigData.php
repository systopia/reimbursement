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

namespace Civi\Reimbursement;

/**
 * @phpstan-type caseTypeConfigDataT array{
 *   id: int,
 *   case_type_id: int,
 *   "case_type_id:name": string,
 *   "case_type_id:label": string,
 *   initial_case_status_id: int|null,
 *   submit_case_status_id: int|null,
 *   writable_case_status_ids: list<int>,
 *   expense_type_ids: non-empty-list<int>,
 *   expense_status_id: int,
 *   save_button_label: string|null,
 *   submit_button_label: string|null,
 *   subject_field_enabled: bool,
 *   subject_field_label: string|null,
 *   subject_field_description: string|null,
 *   details_field_enabled: bool,
 *   details_field_label: string|null,
 *   details_field_description: string|null,
 *   start_date_field_enabled: bool,
 *   start_date_field_label: string|null,
 *   start_date_field_description: string|null,
 *   end_date_field_enabled: bool,
 *   end_date_field_label: string|null,
 *   end_date_field_description: string|null,
 * }
 */
final class CaseTypeConfigData {

  private int $id;

  private int $caseTypeId;

  private string $caseTypeName;

  private string $caseTypeLabel;

  private ?int $initialCaseStatusId;

  private ?int $submitCaseStatusId;

  /**
   * @var list<int>
   */
  private array $writableCaseStatusIds;

  /**
   * @var non-empty-list<int>
   */
  private array $expenseTypeIds;

  private int $expenseStatusId;

  private ?string $saveButtonLabel;

  private ?string $submitButtonLabel;

  private bool $subjectFieldEnabled;

  private ?string $subjectFieldLabel;

  private string $subjectFieldDescription;

  private bool $detailsFieldEnabled;

  private ?string $detailsFieldLabel;

  private string $detailsFieldDescription;

  private bool $startDateFieldEnabled;

  private ?string $startDateFieldLabel;

  private string $startDateFieldDescription;

  private bool $endDateFieldEnabled;

  private ?string $endDateFieldLabel;

  private string $endDateFieldDescription;

  /**
   * @phpstan-param caseTypeConfigDataT $data
   */
  public function __construct(array $data) {
    $this->id = $data['id'];
    $this->caseTypeId = $data['case_type_id'];
    $this->caseTypeName = $data['case_type_id:name'];
    $this->caseTypeLabel = $data['case_type_id:label'];
    $this->initialCaseStatusId = $data['initial_case_status_id'];
    $this->submitCaseStatusId = $data['submit_case_status_id'];
    $this->writableCaseStatusIds = $data['writable_case_status_ids'] ?? [];
    $this->expenseTypeIds = $data['expense_type_ids'];
    $this->expenseStatusId = $data['expense_status_id'];
    $this->saveButtonLabel = $data['save_button_label'] === '' ? NULL : $data['save_button_label'];
    $this->submitButtonLabel = $data['submit_button_label'] === '' ? NULL : $data['submit_button_label'];

    $this->subjectFieldEnabled = $data['subject_field_enabled'];
    $this->subjectFieldLabel = $data['subject_field_label'] === '' ? NULL : $data['subject_field_label'];
    $this->subjectFieldDescription = $data['subject_field_description'] ?? '';

    $this->detailsFieldEnabled = $data['details_field_enabled'];
    $this->detailsFieldLabel = $data['details_field_label'] === '' ? NULL : $data['details_field_label'];
    $this->detailsFieldDescription = $data['details_field_description'] ?? '';

    $this->startDateFieldEnabled = $data['start_date_field_enabled'];
    $this->startDateFieldLabel = $data['start_date_field_label'] === '' ? NULL : $data['start_date_field_label'];
    $this->startDateFieldDescription = $data['start_date_field_description'] ?? '';

    $this->endDateFieldEnabled = $data['end_date_field_enabled'];
    $this->endDateFieldLabel = $data['end_date_field_label'] === '' ? NULL : $data['end_date_field_label'];
    $this->endDateFieldDescription = $data['end_date_field_description'] ?? '';
  }

  public function getId(): int {
    return $this->id;
  }

  public function getCaseTypeId(): int {
    return $this->caseTypeId;
  }

  public function getCaseTypeName(): string {
    return $this->caseTypeName;
  }

  public function getCaseTypeLabel(): string {
    return $this->caseTypeLabel;
  }

  public function getInitialCaseStatusId(): ?int {
    return $this->initialCaseStatusId;
  }

  public function getSubmitCaseStatusId(): ?int {
    return $this->submitCaseStatusId;
  }

  /**
   * @return list<int>
   */
  public function getWritableCaseStatusIds(): array {
    return $this->writableCaseStatusIds;
  }

  /**
   * @return non-empty-list<int>
   */
  public function getExpenseTypeIds(): array {
    return $this->expenseTypeIds;
  }

  public function getExpenseStatusId(): int {
    return $this->expenseStatusId;
  }

  public function getSaveButtonLabel(): ?string {
    return $this->saveButtonLabel;
  }

  public function getSubmitButtonLabel(): ? string {
    return $this->submitButtonLabel;
  }

  public function isSubjectFieldEnabled(): bool {
    return $this->subjectFieldEnabled;
  }

  public function getSubjectFieldLabel(): ?string {
    return $this->subjectFieldLabel;
  }

  public function getSubjectFieldDescription(): string {
    return $this->subjectFieldDescription;
  }

  public function isDetailsFieldEnabled(): bool {
    return $this->detailsFieldEnabled;
  }

  public function getDetailsFieldLabel(): ?string {
    return $this->detailsFieldLabel;
  }

  public function getDetailsFieldDescription(): string {
    return $this->detailsFieldDescription;
  }

  public function isStartDateFieldEnabled(): bool {
    return $this->startDateFieldEnabled;
  }

  public function getStartDateFieldLabel(): ?string {
    return $this->startDateFieldLabel;
  }

  public function getStartDateFieldDescription(): string {
    return $this->startDateFieldDescription;
  }

  public function isEndDateFieldEnabled(): bool {
    return $this->endDateFieldEnabled;
  }

  public function getEndDateFieldLabel(): ?string {
    return $this->endDateFieldLabel;
  }

  public function getEndDateFieldDescription(): string {
    return $this->endDateFieldDescription;
  }

}
