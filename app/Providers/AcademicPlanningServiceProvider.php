<?php

namespace App\Providers;

use App\Services\AcademicPlanning\PrerequisiteValidationService;
use App\Services\AcademicPlanning\ValidationEngine\AcademicPlanningValidationEngine;
use App\Services\AcademicPlanning\ValidationEngine\Validators\CompletedCourseRuleValidator;
use App\Services\AcademicPlanning\ValidationEngine\Validators\GraduationForecastImpactRuleValidator;
use App\Services\AcademicPlanning\ValidationEngine\Validators\InProgressCourseRuleValidator;
use App\Services\AcademicPlanning\ValidationEngine\Validators\PrerequisitePlacementRuleValidator;
use App\Services\AcademicPlanning\ValidationEngine\Validators\PrerequisiteSatisfiedRuleValidator;
use App\Services\AcademicPlanning\ValidationEngine\Validators\ScheduleConflictRuleValidator;
use App\Services\AcademicPlanning\ValidationEngine\Validators\SemesterCreditLoadRuleValidator;
use Illuminate\Support\ServiceProvider;

class AcademicPlanningServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AcademicPlanningValidationEngine::class, function ($app): AcademicPlanningValidationEngine {
            return new AcademicPlanningValidationEngine([
                $app->make(CompletedCourseRuleValidator::class),
                $app->make(InProgressCourseRuleValidator::class),
                new PrerequisiteSatisfiedRuleValidator($app->make(PrerequisiteValidationService::class)),
                $app->make(PrerequisitePlacementRuleValidator::class),
                $app->make(SemesterCreditLoadRuleValidator::class),
                $app->make(ScheduleConflictRuleValidator::class),
                $app->make(GraduationForecastImpactRuleValidator::class),
            ]);
        });
    }
}
