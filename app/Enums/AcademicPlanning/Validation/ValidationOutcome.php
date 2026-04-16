<?php

namespace App\Enums\AcademicPlanning\Validation;

enum ValidationOutcome: string
{
    case VALID = 'valid';
    case WARNING = 'warning';
    case BLOCKED = 'blocked';
}
