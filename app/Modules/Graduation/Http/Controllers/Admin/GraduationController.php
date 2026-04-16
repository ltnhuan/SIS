<?php

namespace App\Modules\Graduation\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Graduation\Models\GraduationCandidate;
use App\Modules\Core\Services\AuditLogger;

class GraduationController extends Controller
{
    public function index()
    {
        $candidates = GraduationCandidate::latest('id')->take(10)->get();
        return view('admin.pages.graduation', compact('candidates'));
    }

    public function approve(AuditLogger $logger)
    {
        $candidate = GraduationCandidate::where('status', 'pending')->latest('id')->first();
        if (! $candidate) {
            return back()->with('status', 'Không có ứng viên cần duyệt.');
        }

        $before = $candidate->toArray();
        $candidate->update(['status' => 'approved']);
        $logger->log('GRADUATION_APPROVED', 'graduation_candidates', $candidate->id, $before, $candidate->toArray(), 1);

        return back()->with('status', 'Đã duyệt ứng viên tốt nghiệp.');
    }
}
