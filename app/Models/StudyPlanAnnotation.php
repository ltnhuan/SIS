<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudyPlanAnnotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'study_plan_version_id',
        'advisor_id',
        'study_plan_item_id',
        'annotation',
    ];

    public function version(): BelongsTo
    {
        return $this->belongsTo(StudyPlanVersion::class, 'study_plan_version_id');
    }

    public function advisor(): BelongsTo
    {
        return $this->belongsTo(Advisor::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(StudyPlanItem::class, 'study_plan_item_id');
    }
}
