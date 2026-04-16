<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicPlanningDashboardSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'study_plan_version_id',
        'snapshot_date',
        'forecast_payload',
        'progress_payload',
        'workload_payload',
        'risk_payload',
        'calculated_at',
    ];

    protected function casts(): array
    {
        return [
            'snapshot_date' => 'date',
            'forecast_payload' => 'array',
            'progress_payload' => 'array',
            'workload_payload' => 'array',
            'risk_payload' => 'array',
            'calculated_at' => 'datetime',
        ];
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(StudyPlanVersion::class, 'study_plan_version_id');
    }
}
