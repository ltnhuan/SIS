<?php

namespace App\Enums\AcademicPlanning;

enum StudyPlanReviewStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REVISION_REQUIRED = 'revision_required';
}
