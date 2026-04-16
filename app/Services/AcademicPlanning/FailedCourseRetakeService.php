<?php

namespace App\Services\AcademicPlanning;

use App\Enums\AcademicPlanning\StudyPlanItemStatus;
use App\Models\FailedCourseRetakeSuggestion;
use App\Models\StudyPlanVersion;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FailedCourseRetakeService
{
    public function markAndSuggest(StudyPlanVersion $version): void
    {
        $studentId = $version->studyPlan->student_id;

        $failedCourseIds = DB::table('course_results')
            ->where('student_id', $studentId)
            ->whereIn('result', ['F', 'FX'])
            ->pluck('course_id');

        if ($failedCourseIds->isEmpty()) {
            return;
        }

        $version->items()
            ->whereIn('course_id', $failedCourseIds)
            ->update([
                'status' => StudyPlanItemStatus::RETAKE_REQUIRED->value,
                'is_failed_retake' => true,
            ]);

        $targetSemesterId = $version->semesters()->orderBy('order_in_plan')->value('semester_id');

        foreach ($failedCourseIds as $courseId) {
            FailedCourseRetakeSuggestion::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'course_id' => $courseId,
                    'study_plan_version_id' => $version->id,
                ],
                [
                    'suggested_semester_id' => $targetSemesterId,
                    'status' => 'pending',
                    'generated_at' => Carbon::now(),
                    'meta' => ['source' => 'phase_1_failed_course_auto_marking'],
                ]
            );
        }
    }
}
