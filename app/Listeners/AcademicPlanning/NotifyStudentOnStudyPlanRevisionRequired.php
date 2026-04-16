<?php

namespace App\Listeners\AcademicPlanning;

use App\Events\AcademicPlanning\StudyPlanRevisionRequested;
use App\Notifications\AcademicPlanning\StudyPlanRevisionRequiredNotification;
use Illuminate\Support\Facades\DB;

class NotifyStudentOnStudyPlanRevisionRequired
{
    public function handle(StudyPlanRevisionRequested $event): void
    {
        $studentUser = DB::table('students')
            ->join('users', 'users.id', '=', 'students.user_id')
            ->where('students.id', $event->version->studyPlan->student_id)
            ->select('users.id')
            ->first();

        if ($studentUser === null) {
            return;
        }

        $userModel = \App\Models\User::find($studentUser->id);
        $userModel?->notify(new StudyPlanRevisionRequiredNotification($event->version));
    }
}
