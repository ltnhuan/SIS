<?php

namespace App\Modules\Timetabling\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Timetabling\Models\TimetableRun;
use App\Modules\Timetabling\Models\TimetablePublication;
use App\Modules\Timetabling\Services\GreedyTimetableSolver;
use App\Modules\Core\Services\AuditLogger;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    public function createRun(Request $request)
    {
        $run = TimetableRun::create([
            'term_id' => $request->input('term_id'),
            'mode' => 'API',
            'status' => 'draft',
            'solver_input_json' => ['source' => 'api'],
            'started_at' => now(),
        ]);

        return response()->json([
            'message' => 'Đã tạo run xếp TKB.',
            'data' => $run,
        ]);
    }

    public function showRun(int $run)
    {
        $runData = TimetableRun::findOrFail($run);

        return response()->json([
            'message' => 'Thông tin run xếp TKB.',
            'data' => $runData,
        ]);
    }

    public function publish(Request $request, GreedyTimetableSolver $solver, AuditLogger $logger)
    {
        $run = TimetableRun::findOrFail($request->input('run_id'));
        $solver->run($run);

        $publication = TimetablePublication::create([
            'term_id' => $request->input('term_id'),
            'version_no' => 1,
            'published_at' => now(),
            'published_by' => 1,
        ]);

        $logger->log('PUBLISH_TKB', 'timetable_publications', $publication->id, [], $publication->toArray(), 1);

        return response()->json([
            'message' => 'Đã công bố thời khóa biểu.',
            'data' => $publication,
        ]);
    }
}
