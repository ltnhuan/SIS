<?php

namespace App\Notifications\AcademicPlanning;

use App\Models\StudyPlanVersion;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StudyPlanSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly StudyPlanVersion $version)
    {
    }

    public function via(object $notifiable): array
    {
        return config('academic_planning.notification_channels', ['database']);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'study_plan_submitted',
            'study_plan_version_id' => $this->version->id,
            'study_plan_id' => $this->version->study_plan_id,
            'student_id' => $this->version->studyPlan->student_id,
            'message' => 'Sinh viên đã nộp kế hoạch học tập để CVHT duyệt.',
        ];
    }
}
