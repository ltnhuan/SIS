<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudyPlanSemester extends Model
{
    use HasFactory;

    protected $fillable = [
        'study_plan_version_id',
        'semester_id',
        'order_in_plan',
        'planned_credits',
        'max_credits',
        'risk_level',
    ];

    public function version(): BelongsTo
    {
        return $this->belongsTo(StudyPlanVersion::class, 'study_plan_version_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StudyPlanItem::class);
    }
}
