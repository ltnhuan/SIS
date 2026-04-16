<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudyPlan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'student_id',
        'advisor_id',
        'academic_program_id',
        'curriculum_id',
        'status',
        'current_version_no',
        'submitted_at',
        'approved_at',
        'latest_reviewer_note',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function versions(): HasMany
    {
        return $this->hasMany(StudyPlanVersion::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function advisor(): BelongsTo
    {
        return $this->belongsTo(Advisor::class);
    }

    public function activeVersion(): BelongsTo
    {
        return $this->belongsTo(StudyPlanVersion::class, 'current_version_no', 'version_no')
            ->whereColumn('study_plan_versions.study_plan_id', 'study_plans.id');
    }
}
