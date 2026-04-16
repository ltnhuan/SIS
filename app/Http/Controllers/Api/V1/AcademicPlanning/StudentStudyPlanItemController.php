<?php

namespace App\Http\Controllers\Api\V1\AcademicPlanning;

use App\Http\Controllers\Controller;
use App\Http\Requests\AcademicPlanning\AddStudyPlanItemRequest;
use App\Http\Resources\AcademicPlanning\StudyPlanItemResource;
use App\Models\StudyPlanVersion;
use App\Services\AcademicPlanning\FailedCourseRetakeService;
use App\Services\AcademicPlanning\PrerequisiteValidationService;
use App\Services\AcademicPlanning\StudyPlanVersionService;
use App\Services\AcademicPlanning\WorkloadAnalysisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentStudyPlanItemController extends Controller
{
    public function __construct(
        private readonly StudyPlanVersionService $studyPlanVersionService,
        private readonly PrerequisiteValidationService $prerequisiteValidationService,
        private readonly WorkloadAnalysisService $workloadAnalysisService,
        private readonly FailedCourseRetakeService $failedCourseRetakeService,
    ) {
    }

    public function store(AddStudyPlanItemRequest $request, StudyPlanVersion $version): JsonResponse
    {
        $item = $this->studyPlanVersionService->addItem(
            $version,
            (int) $request->user()->student_id,
            (int) $request->integer('semester_id'),
            (int) $request->integer('course_id'),
            $this->prerequisiteValidationService,
            $this->workloadAnalysisService,
        );

        $this->failedCourseRetakeService->markAndSuggest($version->fresh('studyPlan'));

        return response()->json([
            'success' => true,
            'message' => 'Thêm học phần vào kế hoạch thành công.',
            'data' => new StudyPlanItemResource($item),
        ], 201);
    }

    public function curriculumGrid(Request $request): JsonResponse
    {
        $curriculumCourses = \DB::table('curriculum_courses')
            ->where('curriculum_id', (int) $request->query('curriculum_id'))
            ->join('courses', 'courses.id', '=', 'curriculum_courses.course_id')
            ->select([
                'curriculum_courses.semester_no',
                'courses.id as course_id',
                'courses.code as course_code',
                'courses.name as course_name',
                'courses.credits',
            ])
            ->orderBy('curriculum_courses.semester_no')
            ->get()
            ->groupBy('semester_no');

        return response()->json([
            'success' => true,
            'message' => 'Lấy khung CTĐT thành công.',
            'data' => $curriculumCourses,
        ]);
    }
}
