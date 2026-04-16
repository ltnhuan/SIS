<?php

namespace App\Enums\AcademicPlanning\Validation;

enum ValidationSeverity: string
{
    case INFO = 'info';
    case WARNING = 'warning';
    case BLOCKING = 'blocking';
}
