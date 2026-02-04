<?php

namespace App\Modules\Curriculum\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Curriculum\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        return response()->json([
            'message' => 'Danh sách học phần.',
            'data' => Course::all(),
        ]);
    }

    public function store(Request $request)
    {
        $course = Course::create($request->only(['tenant_id', 'code', 'name', 'credits', 'department_id', 'requires_lab_bool']));

        return response()->json([
            'message' => 'Tạo học phần thành công.',
            'data' => $course,
        ]);
    }
}
