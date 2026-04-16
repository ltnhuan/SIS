<?php

namespace App\Services\AcademicPlanning;

use App\Enums\AcademicPlanning\StudyPlanStatus;
use App\Enums\AcademicPlanning\StudyPlanVersionStatus;
use App\Models\StudyPlan;
use App\Models\StudyPlanVersion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudyPlanService
{
    public function createPlan(array $payload, int $studentId): StudyPlan
    {
        return DB::transaction(function () use ($payload, $studentId): StudyPlan {
            $plan = StudyPlan::create([
                'student_id' => $studentId,
                'advisor_id' => $payload['advisor_id'] ?? null,
                'academic_program_id' => $payload['academic_program_id'],
                'curriculum_id' => $payload['curriculum_id'],
                'status' => StudyPlanStatus::DRAFT->value,
                'current_version_no' => 1,
            ]);

            StudyPlanVersion::create([
                'study_plan_id' => $plan->id,
                'version_no' => 1,
                'status' => StudyPlanVersionStatus::DRAFT->value,
                'is_primary' => true,
            ]);

            Log::info('Study plan created', ['study_plan_id' => $plan->id, 'student_id' => $studentId]);

            return $plan->load('versions');
        });
    }

    public function listStudentPlans(int $studentId)
    {
        return StudyPlan::query()
            ->where('student_id', $studentId)
            ->with(['versions'])
            ->latest('id')
            ->get();
    }
}
