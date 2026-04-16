<?php

namespace App\Http\Controllers\Api\V1\AcademicPlanning;

use App\Http\Controllers\Controller;
use App\Http\Requests\AcademicPlanning\CreateStudyPlanRequest;
use App\Http\Resources\AcademicPlanning\StudyPlanResource;
use App\Services\AcademicPlanning\StudyPlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentStudyPlanController extends Controller
{
    public function __construct(private readonly StudyPlanService $studyPlanService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $plans = $this->studyPlanService->listStudentPlans((int) $request->user()->student_id);

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách kế hoạch thành công.',
            'data' => StudyPlanResource::collection($plans),
        ]);
    }

    public function store(CreateStudyPlanRequest $request): JsonResponse
    {
        $plan = $this->studyPlanService->createPlan($request->validated(), (int) $request->user()->student_id);

        return response()->json([
            'success' => true,
            'message' => 'Tạo kế hoạch học tập thành công.',
            'data' => new StudyPlanResource($plan),
        ], 201);
    }
}
