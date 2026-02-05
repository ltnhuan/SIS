<?php

namespace App\Modules\Grades\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Grades\Models\GradeBook;
use App\Modules\Grades\Models\GradeEntry;
use App\Modules\Core\Services\AuditLogger;
use App\Modules\Core\Services\RbacService;
use Illuminate\Http\Request;

class GradesController extends Controller
{
    public function index()
    {
        $gradeBooks = GradeBook::latest('id')->take(5)->get();
        return view('admin.pages.grades', compact('gradeBooks'));
    }

    public function randomize()
    {
        $entries = GradeEntry::take(10)->get();
        foreach ($entries as $entry) {
            $entry->update([
                'score' => rand(50, 95) / 10,
                'status' => 'submitted',
            ]);
        }

        return back()->with('status', 'Đã chấm điểm ngẫu nhiên cho 10 dòng dữ liệu.');
    }

    public function finalize(AuditLogger $logger, RbacService $rbac, Request $request)
    {
        $userId = $request->user()?->id ?? 1;
        if (! $rbac->userHasRoleInScope($userId, ['dao_tao', 'admin_truong'], 'campus', 1)) {
            return back()->with('status', 'Bạn không có quyền khóa sổ điểm.');
        }

        $gradeBook = GradeBook::latest('id')->first();
        if (! $gradeBook) {
            return back()->with('status', 'Chưa có sổ điểm để khóa.');
        }

        $before = $gradeBook->toArray();
        $gradeBook->update(['status' => 'finalized']);
        $logger->log('GRADE_BOOK_FINALIZE', 'grade_books', $gradeBook->id, $before, $gradeBook->toArray(), 1);

        return back()->with('status', 'Đã khóa sổ điểm.');
    }
}
