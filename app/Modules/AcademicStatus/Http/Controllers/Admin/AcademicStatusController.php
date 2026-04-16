<?php

namespace App\Modules\AcademicStatus\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Grades\Models\GpaSnapshot;
use App\Modules\AcademicStatus\Models\AcademicWarning;
use App\Modules\Core\Services\AuditLogger;

class AcademicStatusController extends Controller
{
    public function index()
    {
        $warnings = AcademicWarning::latest('created_at')->take(10)->get();
        return view('admin.pages.academic-status', compact('warnings'));
    }

    public function computeGpa(AuditLogger $logger)
    {
        $snapshot = GpaSnapshot::create([
            'student_id' => 1,
            'term_id' => 1,
            'gpa' => 3.1,
            'cpa' => 2.9,
            'detail_json' => ['calc' => 'demo'],
            'computed_at' => now(),
        ]);

        AcademicWarning::create([
            'student_id' => 1,
            'term_id' => 1,
            'level' => 1,
            'reason' => 'CPA dưới ngưỡng cảnh báo.',
            'created_at' => now(),
        ]);

        $logger->log('GPA_COMPUTE', 'gpa_snapshots', $snapshot->id, [], $snapshot->toArray(), 1);

        return back()->with('status', 'Đã tính GPA/CPA và sinh cảnh báo học vụ.');
    }
}
