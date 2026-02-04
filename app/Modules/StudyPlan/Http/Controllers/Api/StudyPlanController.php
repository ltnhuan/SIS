<?php

namespace App\Modules\StudyPlan\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\StudyPlan\Models\CohortStudyPlan;
use App\Modules\StudyPlan\Models\CohortStudyPlanItem;
use App\Modules\Curriculum\Models\CurriculumVersionCourse;
use Illuminate\Http\Request;

class StudyPlanController extends Controller
{
    public function create(Request $request, int $cohort)
    {
        $versionId = $request->input('curriculum_version_id', 1);
        $plan = CohortStudyPlan::create([
            'cohort_id' => $cohort,
            'curriculum_version_id' => $versionId,
            'term_no' => 1,
        ]);

        $courses = CurriculumVersionCourse::where('curriculum_version_id', $versionId)
            ->where('recommended_term', 1)
            ->get();

        foreach ($courses as $course) {
            CohortStudyPlanItem::create([
                'cohort_study_plan_id' => $plan->id,
                'course_id' => $course->course_id,
            ]);
        }

        return response()->json([
            'message' => 'Đã tạo kế hoạch theo khóa.',
            'data' => $plan,
        ]);
    }
}
