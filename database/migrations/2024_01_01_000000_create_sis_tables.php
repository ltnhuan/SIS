<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        Schema::create('campuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->string('code');
            $table->string('name');
        });

        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campus_id')->index();
            $table->string('code');
            $table->string('name');
        });

        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->index();
            $table->string('code');
            $table->string('name');
        });

        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->string('code');
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
        });

        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campus_id')->index();
            $table->string('code');
            $table->string('name');
            $table->integer('capacity');
            $table->json('equipment_json')->nullable();
        });

        Schema::create('time_slot_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->string('code');
            $table->string('name');
        });

        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->index();
            $table->integer('weekday');
            $table->time('starts_at');
            $table->time('ends_at');
            $table->boolean('is_enabled')->default(true);
        });

        Schema::create('calendar_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->string('scope_type');
            $table->unsignedBigInteger('scope_id');
            $table->string('type');
            $table->dateTime('start_dt');
            $table->dateTime('end_dt');
            $table->string('repeat_rule')->nullable();
            $table->string('note')->nullable();
        });

        Schema::create('policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->string('scope_type');
            $table->unsignedBigInteger('scope_id');
            $table->integer('priority')->default(1);
            $table->string('rule_type');
            $table->json('params_json')->nullable();
            $table->dateTime('active_from')->nullable();
            $table->dateTime('active_to')->nullable();
            $table->boolean('is_enabled')->default(true);
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->string('code');
            $table->string('name');
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->foreignId('user_id')->index();
            $table->foreignId('role_id')->index();
            $table->string('scope_type');
            $table->unsignedBigInteger('scope_id');
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->foreignId('permission_id')->index();
            $table->foreignId('role_id')->index();
        });

        Schema::create('access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('route');
            $table->string('ip')->nullable();
            $table->text('user_agent')->nullable();
            $table->dateTime('created_at');
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->nullable()->index();
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id');
            $table->string('action');
            $table->json('before_json')->nullable();
            $table->json('after_json')->nullable();
            $table->dateTime('created_at');
        });

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->string('code');
            $table->string('full_name');
            $table->foreignId('cohort_id')->index();
            $table->foreignId('program_id')->index();
            $table->foreignId('curriculum_version_id')->index();
            $table->json('status_json')->nullable();
        });

        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->string('code');
            $table->string('full_name');
            $table->foreignId('department_id')->index();
            $table->json('workload_json')->nullable();
            $table->json('skills_json')->nullable();
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->string('code');
            $table->string('name');
            $table->integer('credits');
            $table->foreignId('department_id')->index();
            $table->boolean('requires_lab_bool')->default(false);
        });

        Schema::create('curriculums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->foreignId('program_id')->index();
            $table->string('name');
        });

        Schema::create('curriculum_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_id')->index();
            $table->string('version_code');
            $table->date('effective_from');
            $table->string('status');
        });

        Schema::create('curriculum_version_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_version_id')->index();
            $table->foreignId('course_id')->index();
            $table->integer('recommended_term');
            $table->boolean('compulsory')->default(true);
        });

        Schema::create('course_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_version_id')->index();
            $table->foreignId('course_id')->index();
            $table->string('rule_type');
            $table->json('expression_json')->nullable();
        });

        Schema::create('cohorts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->foreignId('program_id')->index();
            $table->string('code');
            $table->string('name');
            $table->integer('entry_year');
        });

        Schema::create('cohort_study_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cohort_id')->index();
            $table->foreignId('curriculum_version_id')->index();
            $table->integer('term_no');
        });

        Schema::create('cohort_study_plan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cohort_study_plan_id')->index();
            $table->foreignId('course_id')->index();
        });

        Schema::create('alert_thresholds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->string('type');
            $table->json('params_json')->nullable();
            $table->boolean('is_enabled')->default(true);
        });

        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->index();
            $table->string('type');
            $table->string('severity');
            $table->json('snapshot_json')->nullable();
            $table->string('status');
            $table->dateTime('created_at');
        });

        Schema::create('enrollment_windows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('term_id')->index();
            $table->dateTime('open_at');
            $table->dateTime('close_at');
            $table->integer('max_credits');
        });

        Schema::create('class_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('term_id')->index();
            $table->foreignId('course_id')->index();
            $table->string('code');
            $table->integer('capacity_min');
            $table->integer('capacity_max');
            $table->string('status');
            $table->foreignId('teacher_id')->index();
            $table->boolean('requires_lab_bool')->default(false);
        });

        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->index();
            $table->foreignId('class_section_id')->index();
            $table->string('status');
            $table->dateTime('created_at');
        });

        Schema::create('waitlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->index();
            $table->foreignId('class_section_id')->index();
            $table->integer('priority');
            $table->string('status');
            $table->dateTime('created_at');
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->string('type');
            $table->foreignId('requester_id')->index();
            $table->string('status');
            $table->dateTime('sla_due_at')->nullable();
            $table->json('payload_json')->nullable();
            $table->dateTime('created_at');
        });

        Schema::create('workflow_instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->index();
            $table->string('current_step');
            $table->string('status');
            $table->json('history_json')->nullable();
        });

        Schema::create('timetable_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('term_id')->index();
            $table->string('mode');
            $table->string('status');
            $table->json('solver_input_json')->nullable();
            $table->json('solver_output_json')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('finished_at')->nullable();
        });

        Schema::create('timetable_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('timetable_run_id')->index();
            $table->foreignId('class_section_id')->index();
            $table->foreignId('room_id')->index();
            $table->foreignId('time_slot_id')->index();
            $table->foreignId('teacher_id')->index();
            $table->string('status');
        });

        Schema::create('timetable_conflicts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('timetable_run_id')->index();
            $table->string('conflict_type');
            $table->json('detail_json')->nullable();
        });

        Schema::create('timetable_publications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('term_id')->index();
            $table->integer('version_no');
            $table->dateTime('published_at');
            $table->foreignId('published_by')->index();
        });

        Schema::create('timetable_change_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('term_id')->index();
            $table->string('type');
            $table->foreignId('requested_by')->index();
            $table->string('status');
            $table->string('reason')->nullable();
            $table->json('payload_json')->nullable();
            $table->dateTime('created_at');
        });

        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_section_id')->index();
            $table->date('session_date');
            $table->foreignId('time_slot_id')->index();
            $table->string('qr_token');
            $table->dateTime('expires_at');
        });

        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_session_id')->index();
            $table->foreignId('student_id')->index();
            $table->string('status');
            $table->foreignId('marked_by')->nullable()->index();
            $table->dateTime('marked_at');
        });

        Schema::create('attendance_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->string('scope_type');
            $table->unsignedBigInteger('scope_id');
            $table->integer('threshold_percent');
            $table->boolean('is_enabled')->default(true);
        });

        Schema::create('exam_eligibilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->index();
            $table->foreignId('course_id')->index();
            $table->foreignId('term_id')->index();
            $table->boolean('is_eligible')->default(true);
            $table->string('reason')->nullable();
            $table->dateTime('updated_at');
        });

        Schema::create('grading_scales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->string('code');
            $table->json('params_json')->nullable();
        });

        Schema::create('grade_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_section_id')->index();
            $table->string('status');
        });

        Schema::create('grade_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_book_id')->index();
            $table->foreignId('student_id')->index();
            $table->decimal('score', 4, 2)->nullable();
            $table->string('status');
        });

        Schema::create('gpa_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->index();
            $table->foreignId('term_id')->index();
            $table->decimal('gpa', 4, 2);
            $table->decimal('cpa', 4, 2);
            $table->json('detail_json')->nullable();
            $table->dateTime('computed_at');
        });

        Schema::create('academic_warnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->index();
            $table->foreignId('term_id')->index();
            $table->integer('level');
            $table->string('reason');
            $table->dateTime('created_at');
        });

        Schema::create('graduation_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->foreignId('program_id')->index();
            $table->json('params_json')->nullable();
            $table->boolean('is_enabled')->default(true);
        });

        Schema::create('graduation_candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('term_id')->index();
            $table->foreignId('student_id')->index();
            $table->string('status');
            $table->json('detail_json')->nullable();
        });

        Schema::create('graduation_committees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('term_id')->index();
            $table->string('name');
            $table->json('members_json')->nullable();
        });

        Schema::create('graduation_minutes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('committee_id')->index();
            $table->json('content_json')->nullable();
            $table->dateTime('created_at');
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->string('source_type');
            $table->unsignedBigInteger('source_id');
            $table->string('category');
            $table->string('severity');
            $table->string('recipient_type');
            $table->unsignedBigInteger('recipient_id');
            $table->string('title');
            $table->text('body');
            $table->json('payload_json')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('read_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('graduation_minutes');
        Schema::dropIfExists('graduation_committees');
        Schema::dropIfExists('graduation_candidates');
        Schema::dropIfExists('graduation_rules');
        Schema::dropIfExists('academic_warnings');
        Schema::dropIfExists('gpa_snapshots');
        Schema::dropIfExists('grade_entries');
        Schema::dropIfExists('grade_books');
        Schema::dropIfExists('grading_scales');
        Schema::dropIfExists('exam_eligibilities');
        Schema::dropIfExists('attendance_policies');
        Schema::dropIfExists('attendance_records');
        Schema::dropIfExists('attendance_sessions');
        Schema::dropIfExists('timetable_change_requests');
        Schema::dropIfExists('timetable_publications');
        Schema::dropIfExists('timetable_conflicts');
        Schema::dropIfExists('timetable_assignments');
        Schema::dropIfExists('timetable_runs');
        Schema::dropIfExists('workflow_instances');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('waitlists');
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('class_sections');
        Schema::dropIfExists('enrollment_windows');
        Schema::dropIfExists('alerts');
        Schema::dropIfExists('alert_thresholds');
        Schema::dropIfExists('cohort_study_plan_items');
        Schema::dropIfExists('cohort_study_plans');
        Schema::dropIfExists('cohorts');
        Schema::dropIfExists('course_rules');
        Schema::dropIfExists('curriculum_version_courses');
        Schema::dropIfExists('curriculum_versions');
        Schema::dropIfExists('curriculums');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('teachers');
        Schema::dropIfExists('students');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('access_logs');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('users');
        Schema::dropIfExists('policies');
        Schema::dropIfExists('calendar_blocks');
        Schema::dropIfExists('time_slots');
        Schema::dropIfExists('time_slot_templates');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('terms');
        Schema::dropIfExists('programs');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('campuses');
        Schema::dropIfExists('tenants');
    }
};
