<?php

namespace App\Modules\Attendance\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Attendance\Models\AttendanceSession;
use App\Modules\Attendance\Models\AttendanceRecord;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function createSessions(Request $request)
    {
        $termId = $request->input('term_id');
        $sessions = [];

        for ($i = 0; $i < 7; $i++) {
            $sessions[] = AttendanceSession::create([
                'class_section_id' => 1,
                'session_date' => now()->addDays($i)->toDateString(),
                'time_slot_id' => 1,
                'qr_token' => 'APIQR' . now()->timestamp . $i,
                'expires_at' => now()->addDays($i)->addHours(2),
            ]);
        }

        return response()->json([
            'message' => 'Đã tạo buổi điểm danh 7 ngày tới.',
            'data' => $sessions,
        ]);
    }

    public function mark(Request $request)
    {
        $record = AttendanceRecord::create([
            'attendance_session_id' => $request->input('session_id'),
            'student_id' => $request->input('student_id'),
            'status' => $request->input('status', 'present'),
            'marked_by' => $request->user()?->id,
            'marked_at' => now(),
        ]);

        return response()->json([
            'message' => 'Đã ghi nhận điểm danh.',
            'data' => $record,
        ]);
    }
}
