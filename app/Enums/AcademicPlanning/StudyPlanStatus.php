<?php

namespace App\Enums\AcademicPlanning;

enum StudyPlanStatus: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case ADVISOR_APPROVED = 'advisor_approved';
    case REVISION_REQUIRED = 'revision_required';
    case APPROVED = 'approved';
}
