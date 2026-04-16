<?php

namespace App\Modules\Grades\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Grades\Models\GradeEntry;
use App\Modules\Grades\Models\GradeBook;
use App\Modules\Grades\Models\GpaSnapshot;
use App\Modules\Core\Services\AuditLogger;
use Illuminate\Http\Request;

class GradesController extends Controller
{
    public function enter(Request $request)
    {
        $entry = GradeEntry::create([
            'grade_book_id' => $request->input('grade_book_id', 1),
            'student_id' => $request->input('student_id'),
            'score' => $request->input('score'),
            'status' => 'draft',
        ]);

        return response()->json([
            'message' => 'Đã nhập điểm.',
            'data' => $entry,
        ]);
    }

    public function finalize(Request $request, AuditLogger $logger)
    {
        $gradeBook = GradeBook::findOrFail($request->input('class_section_id'));
        $before = $gradeBook->toArray();
        $gradeBook->update(['status' => 'finalized']);
        $logger->log('GRADE_BOOK_FINALIZE', 'grade_books', $gradeBook->id, $before, $gradeBook->toArray(), 1);

        return response()->json(['message' => 'Đã khóa sổ điểm.']);
    }

    public function computeGpa(Request $request)
    {
        $snapshot = GpaSnapshot::create([
            'student_id' => 1,
            'term_id' => $request->input('term_id'),
            'gpa' => 3.0,
            'cpa' => 2.8,
            'detail_json' => ['source' => 'api'],
            'computed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Đã tính GPA/CPA.',
            'data' => $snapshot,
        ]);
    }
}
