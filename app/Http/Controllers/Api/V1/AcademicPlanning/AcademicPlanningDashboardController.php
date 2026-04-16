<?php

namespace App\Http\Controllers\Api\V1\AcademicPlanning;

use App\Http\Controllers\Controller;
use App\Models\StudyPlan;
use Illuminate\Http\JsonResponse;

class AcademicPlanningDashboardController extends Controller
{
    public function overview(): JsonResponse
    {
        $total = StudyPlan::count();
        $approved = StudyPlan::where('status', 'approved')->count();
        $submitted = StudyPlan::where('status', 'submitted')->count();

        return response()->json([
            'success' => true,
            'message' => 'Lấy dữ liệu dashboard KHHT thành công.',
            'data' => [
                'total_study_plans' => $total,
                'approved_study_plans' => $approved,
                'submitted_study_plans' => $submitted,
                'approval_rate' => $total > 0 ? round(($approved / $total) * 100, 2) : 0,
            ],
        ]);
    }
}
