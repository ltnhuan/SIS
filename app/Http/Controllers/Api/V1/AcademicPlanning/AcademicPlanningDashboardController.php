<?php

namespace App\Http\Controllers\Api\V1\AcademicPlanning;

use App\Http\Controllers\Controller;
use App\Http\Resources\AcademicPlanning\Dashboard\AdvisorDashboardResource;
use App\Http\Resources\AcademicPlanning\Dashboard\RegistrarDashboardResource;
use App\Http\Resources\AcademicPlanning\Dashboard\StudentDashboardResource;
use App\Services\AcademicPlanning\Dashboard\AcademicPlanningDashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AcademicPlanningDashboardController extends Controller
{
    public function __construct(private readonly AcademicPlanningDashboardService $dashboardService)
    {
    }

    public function student(Request $request): JsonResponse
    {
        $forceRefresh = $request->boolean('force_refresh', false);
        $data = $this->dashboardService->getStudentDashboard((int) $request->user()->student_id, $forceRefresh);

        return response()->json([
            'success' => true,
            'message' => 'Lấy dashboard KHHT cho sinh viên thành công.',
            'data' => new StudentDashboardResource($data),
        ]);
    }

    public function advisor(Request $request): JsonResponse
    {
        $data = $this->dashboardService->getAdvisorDashboard((int) $request->user()->advisor_id);

        return response()->json([
            'success' => true,
            'message' => 'Lấy dashboard KHHT cho CVHT thành công.',
            'data' => new AdvisorDashboardResource($data),
        ]);
    }

    public function registrar(): JsonResponse
    {
        $data = $this->dashboardService->getRegistrarDashboard();

        return response()->json([
            'success' => true,
            'message' => 'Lấy dashboard KHHT cho Phòng Đào tạo thành công.',
            'data' => new RegistrarDashboardResource($data),
        ]);
    }

    public function queueRecompute(Request $request): JsonResponse
    {
        $this->dashboardService->queueRecompute((int) $request->user()->student_id);

        return response()->json([
            'success' => true,
            'message' => 'Đã đưa yêu cầu tái tính toán dashboard vào queue.',
        ]);
    }
}
