<?php

namespace App\Enums\AcademicPlanning;

enum StudyPlanItemStatus: string
{
    case PLANNED = 'planned';
    case IN_PROGRESS = 'in_progress';
    case PASSED = 'passed';
    case FAILED = 'failed';
    case RETAKE_REQUIRED = 'retake_required';
}
