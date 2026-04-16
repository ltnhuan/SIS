<?php

namespace App\Policies;

use App\Models\StudyPlan;
use App\Models\User;

class StudyPlanPolicy
{
    public function view(User $user, StudyPlan $studyPlan): bool
    {
        return (int) $user->student_id === (int) $studyPlan->student_id || (int) $user->advisor_id === (int) $studyPlan->advisor_id;
    }

    public function update(User $user, StudyPlan $studyPlan): bool
    {
        return (int) $user->student_id === (int) $studyPlan->student_id;
    }

    public function review(User $user, StudyPlan $studyPlan): bool
    {
        return (int) $user->advisor_id === (int) $studyPlan->advisor_id;
    }
}
