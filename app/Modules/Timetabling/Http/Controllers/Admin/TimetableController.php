<?php

namespace App\Modules\Timetabling\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Core\Services\AuditLogger;
use App\Modules\Timetabling\Models\TimetablePublication;
use App\Modules\Timetabling\Models\TimetableRun;
use App\Modules\Timetabling\Services\GreedyTimetableSolver;
use App\Modules\Notification\Models\Notification;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    public function index()
    {
        $runs = TimetableRun::latest('started_at')->take(5)->get();
        $publications = TimetablePublication::latest('published_at')->take(5)->get();

        return view('admin.pages.timetable', compact('runs', 'publications'));
    }

    public function createRun(Request $request)
    {
        $run = TimetableRun::create([
            'term_id' => $request->input('term_id', 1),
            'mode' => 'DEMO',
            'status' => 'draft',
            'solver_input_json' => ['source' => 'manual'],
            'started_at' => now(),
        ]);

        return back()->with('status', 'Đã tạo run xếp TKB #' . $run->id);
    }

    public function runSolver(GreedyTimetableSolver $solver, Request $request)
    {
        $run = TimetableRun::latest('id')->first();
        if (! $run) {
            return back()->with('status', 'Chưa có run để xử lý.');
        }

        $result = $solver->run($run);
        $run->update([
            'status' => 'done',
            'solver_output_json' => [
                'assignments' => count($result['assignments']),
                'conflicts' => count($result['conflicts']),
            ],
            'finished_at' => now(),
        ]);

        return back()->with('status', 'Đã chạy solver demo.');
    }

    public function publish(AuditLogger $logger, Request $request)
    {
        $run = TimetableRun::latest('id')->first();
        if (! $run) {
            return back()->with('status', 'Chưa có run để công bố.');
        }

        $publication = TimetablePublication::create([
            'term_id' => $run->term_id,
            'version_no' => 1,
            'published_at' => now(),
            'published_by' => 1,
        ]);

        Notification::create([
            'tenant_id' => 1,
            'source_type' => 'timetable_publication',
            'source_id' => $publication->id,
            'category' => 'Timetable',
            'severity' => 'Khẩn',
            'recipient_type' => 'all',
            'recipient_id' => 0,
            'title' => 'Công bố thời khóa biểu mới',
            'body' => 'Thời khóa biểu đã được công bố. Vui lòng kiểm tra lịch học.',
            'payload_json' => [],
            'created_at' => now(),
        ]);

        $logger->log('PUBLISH_TKB', 'timetable_publications', $publication->id, [], $publication->toArray(), 1);

        return back()->with('status', 'Đã công bố thời khóa biểu.');
    }
}
