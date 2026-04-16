<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Modules\Core\Models\Tenant;
use App\Modules\Core\Models\Campus;
use App\Modules\Core\Models\Department;
use App\Modules\Core\Models\Program;
use App\Modules\Core\Models\Term;
use App\Modules\Core\Models\Room;
use App\Modules\Core\Models\TimeSlotTemplate;
use App\Modules\Core\Models\TimeSlot;
use App\Modules\Core\Models\CalendarBlock;
use App\Modules\Core\Models\Policy;
use App\Modules\Core\Models\User;
use App\Modules\Core\Models\Role;
use App\Modules\Core\Models\Permission;
use App\Modules\Core\Models\Student;
use App\Modules\Core\Models\Teacher;
use App\Modules\Curriculum\Models\Course;
use App\Modules\Curriculum\Models\Curriculum;
use App\Modules\Curriculum\Models\CurriculumVersion;
use App\Modules\Curriculum\Models\CurriculumVersionCourse;
use App\Modules\Curriculum\Models\CourseRule;
use App\Modules\StudyPlan\Models\Cohort;
use App\Modules\StudyPlan\Models\CohortStudyPlan;
use App\Modules\StudyPlan\Models\CohortStudyPlanItem;
use App\Modules\Enrollment\Models\EnrollmentWindow;
use App\Modules\Enrollment\Models\ClassSection;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Enrollment\Models\Waitlist;
use App\Modules\Attendance\Models\AttendancePolicy;
use App\Modules\Attendance\Models\ExamEligibility;
use App\Modules\Grades\Models\GradingScale;
use App\Modules\Grades\Models\GradeBook;
use App\Modules\Grades\Models\GradeEntry;
use App\Modules\StudyPlan\Models\AlertThreshold;
use App\Modules\StudyPlan\Models\Alert;
use App\Modules\Notification\Models\Notification;
use App\Modules\Timetabling\Models\TimetableChangeRequest;
use App\Modules\Graduation\Models\GraduationCandidate;
use App\Modules\Graduation\Models\GraduationCommittee;
use App\Modules\Graduation\Models\GraduationMinute;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::create(['name' => 'Trường Demo']);
        $campus = Campus::create(['tenant_id' => $tenant->id, 'code' => 'CS1', 'name' => 'Cơ sở 1']);
        $department = Department::create(['campus_id' => $campus->id, 'code' => 'CNTT', 'name' => 'Khoa CNTT']);
        $program = Program::create(['department_id' => $department->id, 'code' => 'KTPM', 'name' => 'Ngành Kỹ thuật phần mềm']);

        $term = Term::create([
            'tenant_id' => $tenant->id,
            'code' => '2026-HK1',
            'name' => 'Học kỳ 1 - 2026',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addWeeks(8)->toDateString(),
        ]);

        Room::create([
            'campus_id' => $campus->id,
            'code' => 'P101',
            'name' => 'Phòng 101',
            'capacity' => 60,
            'equipment_json' => [],
        ]);

        Room::create([
            'campus_id' => $campus->id,
            'code' => 'LAB1',
            'name' => 'Phòng Lab 1',
            'capacity' => 30,
            'equipment_json' => ['lab' => true],
        ]);

        $template = TimeSlotTemplate::create([
            'tenant_id' => $tenant->id,
            'code' => 'TEMPLATE-STD',
            'name' => 'Khung giờ chuẩn',
        ]);

        foreach ([1, 2, 3, 4, 5] as $weekday) {
            TimeSlot::create([
                'template_id' => $template->id,
                'weekday' => $weekday,
                'starts_at' => '07:30:00',
                'ends_at' => '09:30:00',
                'is_enabled' => true,
            ]);
            TimeSlot::create([
                'template_id' => $template->id,
                'weekday' => $weekday,
                'starts_at' => '09:45:00',
                'ends_at' => '11:45:00',
                'is_enabled' => true,
            ]);
        }

        CalendarBlock::create([
            'tenant_id' => $tenant->id,
            'scope_type' => 'tenant',
            'scope_id' => $tenant->id,
            'type' => 'HOLIDAY',
            'start_dt' => now()->addDays(10),
            'end_dt' => now()->addDays(12),
            'note' => 'Nghỉ lễ demo',
        ]);

        Policy::create([
            'tenant_id' => $tenant->id,
            'scope_type' => 'tenant',
            'scope_id' => $tenant->id,
            'priority' => 1,
            'rule_type' => 'ROOM_EQUIPMENT_REQUIRED',
            'params_json' => ['requires' => 'lab'],
            'is_enabled' => true,
        ]);

        Policy::create([
            'tenant_id' => $tenant->id,
            'scope_type' => 'teacher',
            'scope_id' => 1,
            'priority' => 1,
            'rule_type' => 'BLOCK_TIME',
            'params_json' => ['teacher_id' => 1, 'weekday' => 3],
            'is_enabled' => true,
        ]);

        Policy::create([
            'tenant_id' => $tenant->id,
            'scope_type' => 'tenant',
            'scope_id' => $tenant->id,
            'priority' => 2,
            'rule_type' => 'NO_LATE_CLASS',
            'params_json' => ['note' => 'Demo policy'],
            'is_enabled' => true,
        ]);

        User::create([
            'name' => 'Admin Demo',
            'email' => 'admin@demo.local',
            'password' => Hash::make('password'),
        ]);

        $roles = [
            ['code' => 'admin_truong', 'name' => 'Admin Trường'],
            ['code' => 'dao_tao', 'name' => 'Đào tạo'],
            ['code' => 'khoa', 'name' => 'Khoa'],
            ['code' => 'giang_vien', 'name' => 'Giảng viên'],
            ['code' => 'sinh_vien', 'name' => 'Sinh viên'],
            ['code' => 'phu_huynh', 'name' => 'Phụ huynh'],
        ];
        foreach ($roles as $role) {
            Role::create(['tenant_id' => $tenant->id] + $role);
        }

        Permission::create(['code' => 'timetable.publish', 'name' => 'Công bố thời khóa biểu']);
        Permission::create(['code' => 'grades.finalize', 'name' => 'Khóa sổ điểm']);

        $adminRoleId = Role::where('code', 'admin_truong')->value('id');
        $daoTaoRoleId = Role::where('code', 'dao_tao')->value('id');

        DB::table('role_user')->insert([
            [
                'user_id' => 1,
                'role_id' => $adminRoleId,
                'scope_type' => 'campus',
                'scope_id' => $campus->id,
            ],
            [
                'user_id' => 1,
                'role_id' => $daoTaoRoleId,
                'scope_type' => 'campus',
                'scope_id' => $campus->id,
            ],
        ]);

        $teacher1 = Teacher::create([
            'tenant_id' => $tenant->id,
            'code' => 'T01',
            'full_name' => 'GV Trần Văn A',
            'department_id' => $department->id,
        ]);
        $teacher2 = Teacher::create([
            'tenant_id' => $tenant->id,
            'code' => 'T02',
            'full_name' => 'GV Nguyễn Thị B',
            'department_id' => $department->id,
        ]);

        $cohort = Cohort::create([
            'tenant_id' => $tenant->id,
            'program_id' => $program->id,
            'code' => 'K26',
            'name' => 'Khóa 26',
            'entry_year' => 2026,
        ]);

        $curriculum = Curriculum::create([
            'tenant_id' => $tenant->id,
            'program_id' => $program->id,
            'name' => 'CTĐT KTPM',
        ]);

        $version = CurriculumVersion::create([
            'curriculum_id' => $curriculum->id,
            'version_code' => 'v1.0',
            'effective_from' => now()->toDateString(),
            'status' => 'active',
        ]);

        $course1 = Course::create([
            'tenant_id' => $tenant->id,
            'code' => 'C101',
            'name' => 'Nhập môn lập trình',
            'credits' => 3,
            'department_id' => $department->id,
            'requires_lab_bool' => false,
        ]);
        $course2 = Course::create([
            'tenant_id' => $tenant->id,
            'code' => 'C103',
            'name' => 'Thực hành phòng Lab',
            'credits' => 3,
            'department_id' => $department->id,
            'requires_lab_bool' => true,
        ]);
        $course3 = Course::create([
            'tenant_id' => $tenant->id,
            'code' => 'C102',
            'name' => 'Cấu trúc dữ liệu',
            'credits' => 4,
            'department_id' => $department->id,
            'requires_lab_bool' => false,
        ]);

        CurriculumVersionCourse::create([
            'curriculum_version_id' => $version->id,
            'course_id' => $course1->id,
            'recommended_term' => 1,
            'compulsory' => true,
        ]);
        CurriculumVersionCourse::create([
            'curriculum_version_id' => $version->id,
            'course_id' => $course2->id,
            'recommended_term' => 1,
            'compulsory' => true,
        ]);
        CurriculumVersionCourse::create([
            'curriculum_version_id' => $version->id,
            'course_id' => $course3->id,
            'recommended_term' => 2,
            'compulsory' => true,
        ]);

        CourseRule::create([
            'curriculum_version_id' => $version->id,
            'course_id' => $course3->id,
            'rule_type' => 'PREREQ',
            'expression_json' => ['completed' => ['C101']],
        ]);

        $plan = CohortStudyPlan::create([
            'cohort_id' => $cohort->id,
            'curriculum_version_id' => $version->id,
            'term_no' => 1,
        ]);

        CohortStudyPlanItem::create([
            'cohort_study_plan_id' => $plan->id,
            'course_id' => $course1->id,
        ]);
        CohortStudyPlanItem::create([
            'cohort_study_plan_id' => $plan->id,
            'course_id' => $course2->id,
        ]);

        AlertThreshold::create([
            'tenant_id' => $tenant->id,
            'type' => 'EARLY_WARNING',
            'params_json' => ['gpa_min' => 2.0],
            'is_enabled' => true,
        ]);

        Alert::create([
            'student_id' => 1,
            'type' => 'EARLY_WARNING',
            'severity' => 'Cao',
            'snapshot_json' => ['note' => 'Cảnh báo sớm demo'],
            'status' => 'open',
            'created_at' => now(),
        ]);

        $class1 = ClassSection::create([
            'term_id' => $term->id,
            'course_id' => $course1->id,
            'code' => 'LHP-C101-01',
            'capacity_min' => 20,
            'capacity_max' => 60,
            'status' => 'open',
            'teacher_id' => $teacher1->id,
            'requires_lab_bool' => false,
        ]);
        $class2 = ClassSection::create([
            'term_id' => $term->id,
            'course_id' => $course2->id,
            'code' => 'LHP-C103-01',
            'capacity_min' => 15,
            'capacity_max' => 30,
            'status' => 'open',
            'teacher_id' => $teacher2->id,
            'requires_lab_bool' => true,
        ]);

        EnrollmentWindow::create([
            'term_id' => $term->id,
            'open_at' => now()->subDay(),
            'close_at' => now()->addDays(10),
            'max_credits' => 10,
        ]);

        $gradeBook1 = GradeBook::create([
            'class_section_id' => $class1->id,
            'status' => 'draft',
        ]);

        $gradeBook2 = GradeBook::create([
            'class_section_id' => $class2->id,
            'status' => 'draft',
        ]);

        for ($i = 1; $i <= 40; $i++) {
            $student = Student::create([
                'tenant_id' => $tenant->id,
                'code' => 'SV' . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'full_name' => 'Sinh viên ' . $i,
                'cohort_id' => $cohort->id,
                'program_id' => $program->id,
                'curriculum_version_id' => $version->id,
                'status_json' => ['status' => 'active'],
            ]);

            if ($i <= 30) {
                Enrollment::create([
                    'student_id' => $student->id,
                    'class_section_id' => $class1->id,
                    'status' => 'enrolled',
                    'created_at' => now(),
                ]);

                GradeEntry::create([
                    'grade_book_id' => $gradeBook1->id,
                    'student_id' => $student->id,
                    'score' => null,
                    'status' => 'draft',
                ]);
            }

            if ($i <= 25) {
                Enrollment::create([
                    'student_id' => $student->id,
                    'class_section_id' => $class2->id,
                    'status' => 'enrolled',
                    'created_at' => now(),
                ]);

                GradeEntry::create([
                    'grade_book_id' => $gradeBook2->id,
                    'student_id' => $student->id,
                    'score' => null,
                    'status' => 'draft',
                ]);
            } elseif ($i <= 35) {
                Waitlist::create([
                    'student_id' => $student->id,
                    'class_section_id' => $class2->id,
                    'priority' => $i,
                    'status' => 'waiting',
                    'created_at' => now(),
                ]);
            }
        }

        AttendancePolicy::create([
            'tenant_id' => $tenant->id,
            'scope_type' => 'course',
            'scope_id' => $course1->id,
            'threshold_percent' => 80,
            'is_enabled' => true,
        ]);

        ExamEligibility::create([
            'student_id' => 1,
            'course_id' => $course1->id,
            'term_id' => $term->id,
            'is_eligible' => true,
            'reason' => null,
            'updated_at' => now(),
        ]);

        GradingScale::create([
            'tenant_id' => $tenant->id,
            'code' => '10-4',
            'params_json' => [
                ['min' => 8.5, 'gpa' => 4.0],
                ['min' => 7.0, 'gpa' => 3.0],
                ['min' => 5.5, 'gpa' => 2.0],
                ['min' => 4.0, 'gpa' => 1.0],
            ],
        ]);

        Notification::insert([
            [
                'tenant_id' => $tenant->id,
                'source_type' => 'system',
                'source_id' => 0,
                'category' => 'System',
                'severity' => 'Khẩn',
                'recipient_type' => 'all',
                'recipient_id' => 0,
                'title' => 'Thông báo Khẩn',
                'body' => 'Đây là thông báo mức Khẩn.',
                'payload_json' => [],
                'created_at' => now(),
            ],
            [
                'tenant_id' => $tenant->id,
                'source_type' => 'system',
                'source_id' => 0,
                'category' => 'System',
                'severity' => 'Cao',
                'recipient_type' => 'all',
                'recipient_id' => 0,
                'title' => 'Thông báo Cao',
                'body' => 'Đây là thông báo mức Cao.',
                'payload_json' => [],
                'created_at' => now(),
            ],
            [
                'tenant_id' => $tenant->id,
                'source_type' => 'system',
                'source_id' => 0,
                'category' => 'System',
                'severity' => 'Trung',
                'recipient_type' => 'all',
                'recipient_id' => 0,
                'title' => 'Thông báo Trung',
                'body' => 'Đây là thông báo mức Trung.',
                'payload_json' => [],
                'created_at' => now(),
            ],
            [
                'tenant_id' => $tenant->id,
                'source_type' => 'system',
                'source_id' => 0,
                'category' => 'System',
                'severity' => 'Thường',
                'recipient_type' => 'all',
                'recipient_id' => 0,
                'title' => 'Thông báo Thường',
                'body' => 'Đây là thông báo mức Thường.',
                'payload_json' => [],
                'created_at' => now(),
            ],
        ]);

        TimetableChangeRequest::create([
            'term_id' => $term->id,
            'type' => 'CHANGE_REQUEST',
            'requested_by' => $teacher1->id,
            'status' => 'pending',
            'reason' => 'Xin đổi lịch dạy bù (demo).',
            'payload_json' => ['note' => 'Demo change request'],
            'created_at' => now(),
        ]);

        $committee = GraduationCommittee::create([
            'term_id' => $term->id,
            'name' => 'Hội đồng tốt nghiệp Demo',
            'members_json' => ['GV A', 'GV B'],
        ]);

        GraduationCandidate::create([
            'term_id' => $term->id,
            'student_id' => 1,
            'status' => 'pending',
            'detail_json' => ['note' => 'Ứng viên demo'],
        ]);

        GraduationMinute::create([
            'committee_id' => $committee->id,
            'content_json' => ['note' => 'Biên bản demo'],
            'created_at' => now(),
        ]);
    }
}
