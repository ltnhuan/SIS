<?php

namespace App\Modules\AcademicStatus\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Grades\Models\GpaSnapshot;
use App\Modules\AcademicStatus\Models\AcademicWarning;

class DashboardController extends Controller
{
    public function studentSummary(int $student)
    {
        return response()->json([
            'message' => 'Tổng quan sinh viên.',
            'data' => [
                'gpa' => GpaSnapshot::where('student_id', $student)->latest('computed_at')->first(),
                'warnings' => AcademicWarning::where('student_id', $student)->get(),
            ],
        ]);
    }
}
