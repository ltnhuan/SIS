<?php

namespace App\Http\Controllers\Api\V1\AcademicPlanning;

use App\Http\Controllers\Controller;
use App\Http\Requests\AcademicPlanning\CreateStudyPlanAnnotationRequest;
use App\Http\Requests\AcademicPlanning\ReviewStudyPlanRequest;
use App\Http\Resources\AcademicPlanning\StudyPlanReviewResource;
use App\Models\StudyPlanAnnotation;
use App\Models\StudyPlanVersion;
use App\Services\AcademicPlanning\StudyPlanApprovalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdvisorStudyPlanReviewController extends Controller
{
    public function __construct(private readonly StudyPlanApprovalService $studyPlanApprovalService)
    {
    }

    public function pending(Request $request): JsonResponse
    {
        $pending = StudyPlanVersion::query()
            ->where('status', 'submitted')
            ->whereHas('studyPlan', fn ($query) => $query->where('advisor_id', (int) $request->user()->advisor_id))
            ->with('studyPlan')
            ->latest('id')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách kế hoạch chờ duyệt thành công.',
            'data' => $pending,
        ]);
    }

    public function review(ReviewStudyPlanRequest $request, StudyPlanVersion $version): JsonResponse
    {
        $review = $this->studyPlanApprovalService->review(
            $version,
            (int) $request->user()->advisor_id,
            $request->validated('status'),
            $request->validated('comment'),
        );

        return response()->json([
            'success' => true,
            'message' => 'Đánh giá kế hoạch thành công.',
            'data' => new StudyPlanReviewResource($review),
        ]);
    }

    public function annotate(CreateStudyPlanAnnotationRequest $request, StudyPlanVersion $version): JsonResponse
    {
        $annotation = StudyPlanAnnotation::create([
            'study_plan_version_id' => $version->id,
            'advisor_id' => (int) $request->user()->advisor_id,
            'study_plan_item_id' => $request->validated('study_plan_item_id'),
            'annotation' => $request->validated('annotation'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thêm ghi chú CVHT thành công.',
            'data' => $annotation,
        ], 201);
    }
}
