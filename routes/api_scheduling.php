<?php

use App\Http\Controllers\Api\V1\Scheduling\ScheduleController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['auth:sanctum'])->group(function (): void {
    Route::prefix('dt/schedules')->group(function (): void {
        Route::post('check-conflicts', [ScheduleController::class, 'checkConflicts']);
        Route::post('suggest', [ScheduleController::class, 'suggest']);
        Route::get('', [ScheduleController::class, 'index']);
        Route::post('', [ScheduleController::class, 'store']);
        Route::put('{schedule}', [ScheduleController::class, 'update']);
        Route::delete('{schedule}', [ScheduleController::class, 'destroy']);
        Route::post('publish/{semesterId}', [ScheduleController::class, 'publish']);
        Route::put('{schedule}/move', [ScheduleController::class, 'move']);
    });

    Route::get('sv/{id}/timetable', [ScheduleController::class, 'studentTimetable']);
    Route::get('gv/{id}/timetable', [ScheduleController::class, 'teacherTimetable']);
    Route::get('dt/rooms/availability', [ScheduleController::class, 'roomAvailability']);

    Route::get('dt/exam-schedules', [ScheduleController::class, 'examIndex']);
    Route::post('dt/exam-schedules', [ScheduleController::class, 'examStore']);
});
