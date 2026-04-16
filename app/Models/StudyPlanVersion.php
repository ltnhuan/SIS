<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudyPlanVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'study_plan_id',
        'version_no',
        'status',
        'is_primary',
        'total_planned_credits',
        'submitted_at',
        'approved_at',
        'revision_requested_at',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'revision_requested_at' => 'datetime',
        ];
    }

    public function studyPlan(): BelongsTo
    {
        return $this->belongsTo(StudyPlan::class);
    }

    public function semesters(): HasMany
    {
        return $this->hasMany(StudyPlanSemester::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(StudyPlanItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(StudyPlanReview::class);
    }

    public function annotations(): HasMany
    {
        return $this->hasMany(StudyPlanAnnotation::class);
    }

    public function riskFlags(): HasMany
    {
        return $this->hasMany(StudyPlanRiskFlag::class);
    }
}
