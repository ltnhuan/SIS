<?php

namespace App\Http\Controllers\Api\V1\AcademicPlanning;

use App\Http\Controllers\Controller;
use App\Http\Requests\AcademicPlanning\CreateStudyPlanVersionRequest;
use App\Http\Requests\AcademicPlanning\SubmitStudyPlanRequest;
use App\Http\Resources\AcademicPlanning\StudyPlanVersionResource;
use App\Models\StudyPlan;
use App\Models\StudyPlanVersion;
use App\Services\AcademicPlanning\StudyPlanApprovalService;
use App\Services\AcademicPlanning\StudyPlanVersionService;
use Illuminate\Http\JsonResponse;

class StudentStudyPlanVersionController extends Controller
{
    public function __construct(
        private readonly StudyPlanVersionService $studyPlanVersionService,
        private readonly StudyPlanApprovalService $studyPlanApprovalService,
    ) {
    }

    public function store(CreateStudyPlanVersionRequest $request, StudyPlan $studyPlan): JsonResponse
    {
        $version = $this->studyPlanVersionService->createVersion($studyPlan, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Tạo phương án kế hoạch thành công.',
            'data' => new StudyPlanVersionResource($version),
        ], 201);
    }

    public function submit(SubmitStudyPlanRequest $request, StudyPlanVersion $version): JsonResponse
    {
        $submitted = $this->studyPlanApprovalService->submit($version, $request->validated('comment'));

        return response()->json([
            'success' => true,
            'message' => 'Nộp kế hoạch để CVHT duyệt thành công.',
            'data' => new StudyPlanVersionResource($submitted->fresh(['studyPlan'])),
        ]);
    }
}
