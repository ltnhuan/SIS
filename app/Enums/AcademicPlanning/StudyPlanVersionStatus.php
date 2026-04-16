<?php

namespace App\Enums\AcademicPlanning;

enum StudyPlanVersionStatus: string
{
    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case SUBMITTED = 'submitted';
    case REVISION_REQUIRED = 'revision_required';
    case APPROVED = 'approved';
    case ARCHIVED = 'archived';
}
