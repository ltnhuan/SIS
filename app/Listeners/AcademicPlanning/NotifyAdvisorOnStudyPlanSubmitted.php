<?php

namespace App\Listeners\AcademicPlanning;

use App\Events\AcademicPlanning\StudyPlanSubmitted;
use App\Notifications\AcademicPlanning\StudyPlanSubmittedNotification;
use Illuminate\Support\Facades\DB;

class NotifyAdvisorOnStudyPlanSubmitted
{
    public function handle(StudyPlanSubmitted $event): void
    {
        $advisorUser = DB::table('advisors')
            ->join('users', 'users.id', '=', 'advisors.user_id')
            ->where('advisors.id', $event->version->studyPlan->advisor_id)
            ->select('users.id')
            ->first();

        if ($advisorUser === null) {
            return;
        }

        $userModel = \App\Models\User::find($advisorUser->id);
        $userModel?->notify(new StudyPlanSubmittedNotification($event->version));
    }
}
