<?php

namespace App\Services\AcademicPlanning;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PrerequisiteValidationService
{
    public function validateCourseForStudent(int $studentId, int $courseId): array
    {
        $prerequisiteCourseIds = DB::table('curriculum_courses')
            ->where('course_id', $courseId)
            ->whereNotNull('prerequisite_course_id')
            ->pluck('prerequisite_course_id');

        if ($prerequisiteCourseIds->isEmpty()) {
            return ['passed' => true, 'missing_prerequisites' => []];
        }

        $passedCourseIds = DB::table('course_results')
            ->where('student_id', $studentId)
            ->whereIn('course_id', $prerequisiteCourseIds)
            ->whereIn('result', ['A', 'B', 'C', 'D', 'P'])
            ->pluck('course_id');

        $missing = $prerequisiteCourseIds->diff($passedCourseIds)->values()->all();

        return [
            'passed' => empty($missing),
            'missing_prerequisites' => $missing,
        ];
    }
}
