<?php

use App\Http\Controllers\Api\V1\AcademicPlanning\AcademicPlanningDashboardController;
use App\Http\Controllers\Api\V1\AcademicPlanning\AdvisorStudyPlanReviewController;
use App\Http\Controllers\Api\V1\AcademicPlanning\StudentStudyPlanController;
use App\Http\Controllers\Api\V1\AcademicPlanning\StudentStudyPlanItemController;
use App\Http\Controllers\Api\V1\AcademicPlanning\StudentStudyPlanVersionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/academic-planning')->middleware(['auth:sanctum'])->group(function (): void {
    Route::prefix('student')->group(function (): void {
        Route::get('study-plans', [StudentStudyPlanController::class, 'index']);
        Route::post('study-plans', [StudentStudyPlanController::class, 'store']);

        Route::post('study-plans/{studyPlan}/versions', [StudentStudyPlanVersionController::class, 'store']);
        Route::post('study-plan-versions/{version}/submit', [StudentStudyPlanVersionController::class, 'submit']);
        Route::post('study-plan-versions/{version}/items', [StudentStudyPlanItemController::class, 'store']);

        Route::get('curriculum-grid', [StudentStudyPlanItemController::class, 'curriculumGrid']);
    });

    Route::prefix('advisor')->group(function (): void {
        Route::get('study-plan-reviews/pending', [AdvisorStudyPlanReviewController::class, 'pending']);
        Route::post('study-plan-versions/{version}/review', [AdvisorStudyPlanReviewController::class, 'review']);
        Route::post('study-plan-versions/{version}/annotations', [AdvisorStudyPlanReviewController::class, 'annotate']);
    });

    Route::prefix('dashboard')->group(function (): void {
        Route::get('overview', [AcademicPlanningDashboardController::class, 'overview']);
    });
});
