<?php

namespace App\Modules\Attendance\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Attendance\Models\AttendanceSession;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $sessions = AttendanceSession::latest('session_date')->take(10)->get();
        return view('admin.pages.attendance', compact('sessions'));
    }

    public function createSessions(Request $request)
    {
        $termId = $request->input('term_id', 1);
        for ($i = 0; $i < 7; $i++) {
            AttendanceSession::create([
                'class_section_id' => 1,
                'session_date' => now()->addDays($i)->toDateString(),
                'time_slot_id' => 1,
                'qr_token' => 'QR' . now()->timestamp . $i,
                'expires_at' => now()->addDays($i)->addHours(2),
            ]);
        }

        return back()->with('status', 'Đã tạo buổi điểm danh 7 ngày tới cho học kỳ #' . $termId);
    }
}
