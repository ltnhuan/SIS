<?php

namespace App\Listeners\AcademicPlanning;

use App\Events\AcademicPlanning\StudyPlanApproved;
use App\Notifications\AcademicPlanning\StudyPlanApprovedNotification;
use Illuminate\Support\Facades\DB;

class NotifyStudentOnStudyPlanApproved
{
    public function handle(StudyPlanApproved $event): void
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
        $userModel?->notify(new StudyPlanApprovedNotification($event->version));
    }
}
