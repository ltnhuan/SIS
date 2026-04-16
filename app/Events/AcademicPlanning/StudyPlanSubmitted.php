<?php

namespace App\Events\AcademicPlanning;

use App\Models\StudyPlanVersion;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudyPlanSubmitted
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public StudyPlanVersion $version)
    {
    }
}
