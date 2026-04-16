<?php

namespace App\Jobs\AcademicPlanning;

use App\Services\AcademicPlanning\Dashboard\AcademicPlanningDashboardService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecomputeAcademicPlanningDashboardSnapshotJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private readonly int $studentId)
    {
    }

    public function handle(AcademicPlanningDashboardService $dashboardService): void
    {
        $dashboardService->getStudentDashboard($this->studentId, true);
    }
}
