<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudyPlanItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'study_plan_version_id',
        'study_plan_semester_id',
        'course_id',
        'status',
        'credits_snapshot',
        'is_prerequisite_satisfied',
        'is_credit_overload',
        'is_failed_retake',
        'validation_payload',
    ];

    protected function casts(): array
    {
        return [
            'is_prerequisite_satisfied' => 'boolean',
            'is_credit_overload' => 'boolean',
            'is_failed_retake' => 'boolean',
            'validation_payload' => 'array',
        ];
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(StudyPlanVersion::class, 'study_plan_version_id');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(StudyPlanSemester::class, 'study_plan_semester_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
