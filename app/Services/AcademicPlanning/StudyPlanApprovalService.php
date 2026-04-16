<?php

namespace App\Services\AcademicPlanning;

use App\Enums\AcademicPlanning\StudyPlanReviewStatus;
use App\Enums\AcademicPlanning\StudyPlanStatus;
use App\Enums\AcademicPlanning\StudyPlanVersionStatus;
use App\Events\AcademicPlanning\StudyPlanApproved;
use App\Events\AcademicPlanning\StudyPlanRevisionRequested;
use App\Events\AcademicPlanning\StudyPlanSubmitted;
use App\Models\StudyPlanReview;
use App\Models\StudyPlanVersion;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudyPlanApprovalService
{
    public function submit(StudyPlanVersion $version, ?string $comment = null): StudyPlanVersion
    {
        if ($version->items()->count() === 0) {
            abort(422, 'Kế hoạch chưa có học phần nào để nộp duyệt.');
        }

        $invalidPrereqCount = $version->items()->where('is_prerequisite_satisfied', false)->count();
        if ($invalidPrereqCount > 0) {
            abort(422, 'Kế hoạch còn học phần chưa đạt điều kiện tiên quyết.');
        }

        return DB::transaction(function () use ($version, $comment): StudyPlanVersion {
            $version->update([
                'status' => StudyPlanVersionStatus::SUBMITTED->value,
                'submitted_at' => Carbon::now(),
            ]);

            $version->studyPlan()->update([
                'status' => StudyPlanStatus::SUBMITTED->value,
                'submitted_at' => Carbon::now(),
                'latest_reviewer_note' => $comment,
            ]);

            event(new StudyPlanSubmitted($version->fresh(['studyPlan'])));
            Log::info('Study plan submitted', ['version_id' => $version->id]);

            return $version;
        });
    }

    public function review(StudyPlanVersion $version, int $advisorId, string $status, ?string $comment = null): StudyPlanReview
    {
        return DB::transaction(function () use ($version, $advisorId, $status, $comment): StudyPlanReview {
            $review = StudyPlanReview::create([
                'study_plan_version_id' => $version->id,
                'advisor_id' => $advisorId,
                'status' => $status,
                'comment' => $comment,
                'reviewed_at' => Carbon::now(),
            ]);

            if ($status === StudyPlanReviewStatus::APPROVED->value) {
                $version->update([
                    'status' => StudyPlanVersionStatus::APPROVED->value,
                    'approved_at' => Carbon::now(),
                ]);

                $version->studyPlan()->update([
                    'status' => StudyPlanStatus::APPROVED->value,
                    'approved_at' => Carbon::now(),
                    'latest_reviewer_note' => $comment,
                ]);

                event(new StudyPlanApproved($version->fresh(['studyPlan'])));
            }

            if ($status === StudyPlanReviewStatus::REVISION_REQUIRED->value) {
                $version->update([
                    'status' => StudyPlanVersionStatus::REVISION_REQUIRED->value,
                    'revision_requested_at' => Carbon::now(),
                ]);

                $version->studyPlan()->update([
                    'status' => StudyPlanStatus::REVISION_REQUIRED->value,
                    'latest_reviewer_note' => $comment,
                ]);

                event(new StudyPlanRevisionRequested($version->fresh(['studyPlan'])));
            }

            Log::info('Study plan reviewed', ['version_id' => $version->id, 'status' => $status]);

            return $review;
        });
    }
}
