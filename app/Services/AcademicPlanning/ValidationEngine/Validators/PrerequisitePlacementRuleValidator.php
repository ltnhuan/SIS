<?php

namespace App\Services\AcademicPlanning\ValidationEngine\Validators;

use App\Enums\AcademicPlanning\Validation\ValidationSeverity;
use App\Services\AcademicPlanning\ValidationEngine\Contracts\RuleValidatorInterface;
use App\Services\AcademicPlanning\ValidationEngine\DTO\ValidationContext;
use App\Services\AcademicPlanning\ValidationEngine\DTO\ValidationIssue;
use App\Services\AcademicPlanning\ValidationEngine\DTO\ValidationResult;
use Illuminate\Support\Facades\DB;

class PrerequisitePlacementRuleValidator implements RuleValidatorInterface
{
    public function validate(ValidationContext $context, ValidationResult $result): void
    {
        $prerequisites = DB::table('curriculum_courses')
            ->where('course_id', $context->courseId)
            ->whereNotNull('prerequisite_course_id')
            ->pluck('prerequisite_course_id');

        if ($prerequisites->isEmpty()) {
            return;
        }

        $candidateSemesterStartDate = DB::table('semesters')->where('id', $context->semesterId)->value('start_date');
        if ($candidateSemesterStartDate === null) {
            return;
        }

        $plannedPrerequisites = DB::table('study_plan_items')
            ->join('study_plan_semesters', 'study_plan_semesters.id', '=', 'study_plan_items.study_plan_semester_id')
            ->where('study_plan_items.study_plan_version_id', $context->version->id)
            ->whereIn('study_plan_items.course_id', $prerequisites)
            ->pluck('study_plan_semesters.semester_id', 'study_plan_items.course_id');

        $invalidPlacements = [];

        foreach ($prerequisites as $prereqCourseId) {
            if (! isset($plannedPrerequisites[$prereqCourseId])) {
                continue;
            }

            $prereqSemesterStartDate = DB::table('semesters')->where('id', $plannedPrerequisites[$prereqCourseId])->value('start_date');

            if ($prereqSemesterStartDate === null || $prereqSemesterStartDate >= $candidateSemesterStartDate) {
                $invalidPlacements[] = $prereqCourseId;
            }
        }

        if ($invalidPlacements === []) {
            return;
        }

        $result->addIssue(new ValidationIssue(
            code: 'prerequisite.invalid_semester_order',
            severity: ValidationSeverity::BLOCKING->value,
            message: 'Môn tiên quyết phải được xếp ở học kỳ trước.',
            meta: ['invalid_prerequisite_course_ids' => $invalidPlacements],
        ));
    }
}
