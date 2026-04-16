<?php

namespace App\Services\AcademicPlanning;

use App\Enums\AcademicPlanning\StudyPlanItemStatus;
use App\Enums\AcademicPlanning\StudyPlanVersionStatus;
use App\Models\StudyPlan;
use App\Models\StudyPlanItem;
use App\Models\StudyPlanSemester;
use App\Models\StudyPlanVersion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudyPlanVersionService
{
    public function createVersion(StudyPlan $studyPlan, array $payload): StudyPlanVersion
    {
        $versionCount = $studyPlan->versions()->count();
        $max = (int) config('academic_planning.max_versions_per_plan', 3);

        if ($versionCount >= $max) {
            abort(422, 'Đã đạt tối đa số phương án kế hoạch cho phép.');
        }

        return DB::transaction(function () use ($studyPlan, $payload, $versionCount): StudyPlanVersion {
            if (($payload['is_primary'] ?? false) === true) {
                $studyPlan->versions()->update(['is_primary' => false]);
            }

            $version = StudyPlanVersion::create([
                'study_plan_id' => $studyPlan->id,
                'version_no' => $versionCount + 1,
                'status' => StudyPlanVersionStatus::DRAFT->value,
                'is_primary' => (bool) ($payload['is_primary'] ?? false),
            ]);

            Log::info('Study plan version created', ['study_plan_id' => $studyPlan->id, 'version_no' => $version->version_no]);

            return $version;
        });
    }

    public function addItem(
        StudyPlanVersion $version,
        int $studentId,
        int $semesterId,
        int $courseId,
        PrerequisiteValidationService $prerequisiteValidationService,
        WorkloadAnalysisService $workloadAnalysisService
    ): StudyPlanItem {
        return DB::transaction(function () use ($version, $studentId, $semesterId, $courseId, $prerequisiteValidationService, $workloadAnalysisService): StudyPlanItem {
            $semester = StudyPlanSemester::firstOrCreate(
                [
                    'study_plan_version_id' => $version->id,
                    'semester_id' => $semesterId,
                ],
                [
                    'order_in_plan' => $version->semesters()->count() + 1,
                    'planned_credits' => 0,
                    'max_credits' => (int) config('academic_planning.max_credits_per_semester', 24),
                ]
            );

            $course = DB::table('courses')->where('id', $courseId)->first();
            if ($course === null) {
                abort(404, 'Không tìm thấy học phần.');
            }

            $credit = (int) ($course->credits ?? 0);
            $prereq = $prerequisiteValidationService->validateCourseForStudent($studentId, $courseId);
            $workload = $workloadAnalysisService->checkCreditOverload($semester, $credit);

            $item = StudyPlanItem::create([
                'study_plan_version_id' => $version->id,
                'study_plan_semester_id' => $semester->id,
                'course_id' => $courseId,
                'status' => StudyPlanItemStatus::PLANNED->value,
                'credits_snapshot' => $credit,
                'is_prerequisite_satisfied' => $prereq['passed'],
                'is_credit_overload' => $workload['is_overload'],
                'validation_payload' => [
                    'missing_prerequisites' => $prereq['missing_prerequisites'],
                    'workload' => $workload,
                ],
            ]);

            $semester->update([
                'planned_credits' => $workload['planned_credits'],
                'risk_level' => $workload['risk_level'],
            ]);

            $version->update([
                'total_planned_credits' => $version->items()->sum('credits_snapshot'),
            ]);

            Log::info('Study plan item added', [
                'version_id' => $version->id,
                'item_id' => $item->id,
                'course_id' => $courseId,
            ]);

            return $item;
        });
    }
}
