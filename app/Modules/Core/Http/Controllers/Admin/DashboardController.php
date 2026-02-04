<?php

namespace App\Modules\Core\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Student;
use App\Modules\Enrollment\Models\ClassSection;
use App\Modules\Timetabling\Models\TimetableAssignment;
use App\Modules\Attendance\Models\ExamEligibility;
use App\Modules\AcademicStatus\Models\AcademicWarning;
use App\Modules\Notification\Models\Notification;

class DashboardController extends Controller
{
    public function index()
    {
        $studentCount = Student::count();
        $classCount = ClassSection::count();
        $assignedCount = TimetableAssignment::count();
        $eligibleCount = ExamEligibility::where('is_eligible', true)->count();
        $warningCount = AcademicWarning::count();
        $urgentNotifications = Notification::where('severity', 'Khẩn')->count();
        $highNotifications = Notification::where('severity', 'Cao')->count();

        return view('admin.pages.dashboard', compact(
            'studentCount',
            'classCount',
            'assignedCount',
            'eligibleCount',
            'warningCount',
            'urgentNotifications',
            'highNotifications'
        ));
    }

    public function organization()
    {
        return view('admin.pages.organization');
    }

    public function students()
    {
        $students = Student::take(20)->get();
        return view('admin.pages.students', compact('students'));
    }

    public function teachers()
    {
        return view('admin.pages.teachers');
    }

    public function curriculum()
    {
        return view('admin.pages.curriculum');
    }

    public function studyPlan()
    {
        return view('admin.pages.study-plan');
    }

    public function enrollment()
    {
        return view('admin.pages.enrollment');
    }
}
