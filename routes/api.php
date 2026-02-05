<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Core\Http\Controllers\Api\AuthController;
use App\Modules\Core\Http\Controllers\Api\CatalogController;
use App\Modules\Curriculum\Http\Controllers\Api\CurriculumController;
use App\Modules\Curriculum\Http\Controllers\Api\CourseController;
use App\Modules\StudyPlan\Http\Controllers\Api\StudyPlanController;
use App\Modules\Enrollment\Http\Controllers\Api\EnrollmentController;
use App\Modules\Timetabling\Http\Controllers\Api\TimetableController as ApiTimetableController;
use App\Modules\Attendance\Http\Controllers\Api\AttendanceController as ApiAttendanceController;
use App\Modules\Grades\Http\Controllers\Api\GradesController as ApiGradesController;
use App\Modules\AcademicStatus\Http\Controllers\Api\DashboardController as ApiDashboardController;
use App\Modules\Notification\Http\Controllers\Api\NotificationController as ApiNotificationController;

Route::middleware('access.log')->group(function () {
    Route::post('/dang-nhap', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->get('/toi', [AuthController::class, 'me']);

    Route::get('/danh-muc/rooms', [CatalogController::class, 'rooms']);
    Route::get('/danh-muc/terms', [CatalogController::class, 'terms']);
    Route::get('/danh-muc/time-slots', [CatalogController::class, 'timeSlots']);

    Route::get('/ctdt', [CurriculumController::class, 'index']);
    Route::post('/ctdt', [CurriculumController::class, 'store']);
    Route::post('/ctdt/{id}/phien-ban', [CurriculumController::class, 'createVersion']);
    Route::get('/hoc-phan', [CourseController::class, 'index']);
    Route::post('/hoc-phan', [CourseController::class, 'store']);

    Route::post('/khoa/{cohort}/tao-ke-hoach', [StudyPlanController::class, 'create']);

    Route::post('/dang-ky', [EnrollmentController::class, 'enroll']);
    Route::post('/huy-dang-ky', [EnrollmentController::class, 'drop']);

    Route::post('/tkb/runs', [ApiTimetableController::class, 'createRun']);
    Route::get('/tkb/runs/{run}', [ApiTimetableController::class, 'showRun']);
    Route::post('/tkb/cong-bo', [ApiTimetableController::class, 'publish']);

    Route::post('/diem-danh/tao-buoi-7-ngay', [ApiAttendanceController::class, 'createSessions']);
    Route::post('/diem-danh/cham', [ApiAttendanceController::class, 'mark']);

    Route::post('/diem/nhap', [ApiGradesController::class, 'enter']);
    Route::post('/diem/khoa-so', [ApiGradesController::class, 'finalize']);
    Route::post('/gpa/tinh', [ApiGradesController::class, 'computeGpa']);

    Route::get('/sinh-vien/{student}/tong-quan', [ApiDashboardController::class, 'studentSummary']);

    Route::get('/thong-bao', [ApiNotificationController::class, 'index']);
});
