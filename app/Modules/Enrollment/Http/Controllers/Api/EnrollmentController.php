<?php

namespace App\Modules\Enrollment\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Enrollment\Models\Waitlist;
use App\Modules\Enrollment\Models\ClassSection;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function enroll(Request $request)
    {
        $classSection = ClassSection::findOrFail($request->input('class_section_id'));
        $studentId = $request->input('student_id');

        $current = Enrollment::where('class_section_id', $classSection->id)
            ->where('status', 'enrolled')
            ->count();

        if ($current >= $classSection->capacity_max) {
            $waitlist = Waitlist::create([
                'student_id' => $studentId,
                'class_section_id' => $classSection->id,
                'priority' => $current + 1,
                'status' => 'waiting',
                'created_at' => now(),
            ]);

            return response()->json([
                'message' => 'Lớp đầy, đã đưa vào danh sách chờ.',
                'data' => $waitlist,
            ]);
        }

        $enrollment = Enrollment::create([
            'student_id' => $studentId,
            'class_section_id' => $classSection->id,
            'status' => 'enrolled',
            'created_at' => now(),
        ]);

        return response()->json([
            'message' => 'Đăng ký thành công.',
            'data' => $enrollment,
        ]);
    }

    public function drop(Request $request)
    {
        $enrollment = Enrollment::where('student_id', $request->input('student_id'))
            ->where('class_section_id', $request->input('class_section_id'))
            ->first();

        if (! $enrollment) {
            return response()->json(['message' => 'Không tìm thấy đăng ký.'], 404);
        }

        $enrollment->update(['status' => 'dropped']);

        return response()->json(['message' => 'Đã hủy đăng ký.']);
    }
}
