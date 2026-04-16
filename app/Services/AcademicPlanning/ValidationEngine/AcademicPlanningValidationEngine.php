<?php

namespace App\Services\AcademicPlanning\ValidationEngine;

use App\Models\StudyPlanVersion;
use App\Services\AcademicPlanning\ValidationEngine\Contracts\RuleValidatorInterface;
use App\Services\AcademicPlanning\ValidationEngine\DTO\ValidationContext;
use App\Services\AcademicPlanning\ValidationEngine\DTO\ValidationResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AcademicPlanningValidationEngine
{
    /**
     * @param  array<int, RuleValidatorInterface>  $validators
     */
    public function __construct(private readonly array $validators)
    {
    }

    public function validateBeforeAdd(StudyPlanVersion $version, int $studentId, int $semesterId, int $courseId, array $candidateScheduleSlots = []): ValidationResult
    {
        $courseCredits = (int) DB::table('courses')->where('id', $courseId)->value('credits');

        $context = new ValidationContext(
            version: $version,
            studentId: $studentId,
            semesterId: $semesterId,
            courseId: $courseId,
            courseCredits: $courseCredits,
            candidateScheduleSlots: $candidateScheduleSlots,
        );

        $result = new ValidationResult();

        foreach ($this->validators as $validator) {
            $validator->validate($context, $result);
        }

        Log::info('Academic planning validation executed', [
            'study_plan_version_id' => $version->id,
            'student_id' => $studentId,
            'semester_id' => $semesterId,
            'course_id' => $courseId,
            'can_save' => $result->canSave(),
            'outcome' => $result->outcome(),
            'issues_count' => count($result->issues()),
        ]);

        return $result;
    }
}
