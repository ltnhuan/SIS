<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FailedCourseRetakeSuggestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'study_plan_version_id',
        'suggested_semester_id',
        'status',
        'meta',
        'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'generated_at' => 'datetime',
        ];
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(StudyPlanVersion::class, 'study_plan_version_id');
    }
}
