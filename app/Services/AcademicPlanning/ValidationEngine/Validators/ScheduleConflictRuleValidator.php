<?php

namespace App\Services\AcademicPlanning\ValidationEngine\Validators;

use App\Enums\AcademicPlanning\Validation\ValidationSeverity;
use App\Services\AcademicPlanning\ValidationEngine\Contracts\RuleValidatorInterface;
use App\Services\AcademicPlanning\ValidationEngine\DTO\ValidationContext;
use App\Services\AcademicPlanning\ValidationEngine\DTO\ValidationIssue;
use App\Services\AcademicPlanning\ValidationEngine\DTO\ValidationResult;
use Illuminate\Support\Facades\DB;

class ScheduleConflictRuleValidator implements RuleValidatorInterface
{
    public function validate(ValidationContext $context, ValidationResult $result): void
    {
        if ($context->candidateScheduleSlots === []) {
            return;
        }

        $existingItems = DB::table('study_plan_items')
            ->where('study_plan_version_id', $context->version->id)
            ->join('study_plan_semesters', 'study_plan_semesters.id', '=', 'study_plan_items.study_plan_semester_id')
            ->where('study_plan_semesters.semester_id', $context->semesterId)
            ->select(['study_plan_items.course_id', 'study_plan_items.validation_payload'])
            ->get();

        $conflicts = [];

        foreach ($existingItems as $item) {
            $payload = is_string($item->validation_payload) ? json_decode($item->validation_payload, true) : (array) $item->validation_payload;
            $existingSlots = $payload['candidate_schedule_slots'] ?? [];

            foreach ($context->candidateScheduleSlots as $candidateSlot) {
                foreach ($existingSlots as $existingSlot) {
                    if (($candidateSlot['day'] ?? null) !== ($existingSlot['day'] ?? null)) {
                        continue;
                    }

                    $candidateStart = $candidateSlot['start'] ?? null;
                    $candidateEnd = $candidateSlot['end'] ?? null;
                    $existingStart = $existingSlot['start'] ?? null;
                    $existingEnd = $existingSlot['end'] ?? null;

                    if ($candidateStart === null || $candidateEnd === null || $existingStart === null || $existingEnd === null) {
                        continue;
                    }

                    if ($candidateStart < $existingEnd && $candidateEnd > $existingStart) {
                        $conflicts[] = $item->course_id;
                    }
                }
            }
        }

        if ($conflicts === []) {
            return;
        }

        $result->addIssue(new ValidationIssue(
            code: 'schedule.conflict_detected',
            severity: ValidationSeverity::WARNING->value,
            message: 'Có xung đột lịch học với môn khác trong cùng học kỳ.',
            meta: ['conflict_course_ids' => array_values(array_unique($conflicts))],
        ));
    }
}
