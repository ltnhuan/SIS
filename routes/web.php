<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Core\Http\Controllers\Admin\DashboardController;
use App\Modules\Timetabling\Http\Controllers\Admin\TimetableController;
use App\Modules\Attendance\Http\Controllers\Admin\AttendanceController;
use App\Modules\Grades\Http\Controllers\Admin\GradesController;
use App\Modules\AcademicStatus\Http\Controllers\Admin\AcademicStatusController;
use App\Modules\CaseManagement\Http\Controllers\Admin\TicketController;
use App\Modules\Notification\Http\Controllers\Admin\NotificationController;

Route::get('/', function () {
    return redirect('/quan-tri');
});

Route::prefix('quan-tri')->group(function () {
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/to-chuc', [DashboardController::class, 'organization']);
    Route::get('/sinh-vien', [DashboardController::class, 'students']);
    Route::get('/giang-vien', [DashboardController::class, 'teachers']);
    Route::get('/ctdt', [DashboardController::class, 'curriculum']);
    Route::get('/ke-hoach', [DashboardController::class, 'studyPlan']);
    Route::get('/mo-lop', [DashboardController::class, 'enrollment']);
    Route::get('/xep-tkb', [TimetableController::class, 'index']);
    Route::post('/xep-tkb/tao-run', [TimetableController::class, 'createRun']);
    Route::post('/xep-tkb/chay-solver', [TimetableController::class, 'runSolver']);
    Route::post('/xep-tkb/cong-bo', [TimetableController::class, 'publish']);
    Route::get('/diem-danh', [AttendanceController::class, 'index']);
    Route::post('/diem-danh/tao-buoi', [AttendanceController::class, 'createSessions']);
    Route::get('/diem-so', [GradesController::class, 'index']);
    Route::post('/diem-so/cham-ngau-nhien', [GradesController::class, 'randomize']);
    Route::post('/diem-so/khoa-so', [GradesController::class, 'finalize']);
    Route::get('/canh-bao', [AcademicStatusController::class, 'index']);
    Route::post('/canh-bao/tinh-gpa', [AcademicStatusController::class, 'computeGpa']);
    Route::get('/tickets', [TicketController::class, 'index']);
    Route::get('/thong-bao', [NotificationController::class, 'index']);
});
