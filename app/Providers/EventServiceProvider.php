<?php

namespace App\Providers;

use App\Events\AcademicPlanning\StudyPlanApproved;
use App\Events\AcademicPlanning\StudyPlanRevisionRequested;
use App\Events\AcademicPlanning\StudyPlanSubmitted;
use App\Listeners\AcademicPlanning\NotifyAdvisorOnStudyPlanSubmitted;
use App\Listeners\AcademicPlanning\NotifyStudentOnStudyPlanApproved;
use App\Listeners\AcademicPlanning\NotifyStudentOnStudyPlanRevisionRequired;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        StudyPlanSubmitted::class => [
            NotifyAdvisorOnStudyPlanSubmitted::class,
        ],
        StudyPlanRevisionRequested::class => [
            NotifyStudentOnStudyPlanRevisionRequired::class,
        ],
        StudyPlanApproved::class => [
            NotifyStudentOnStudyPlanApproved::class,
        ],
    ];

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
