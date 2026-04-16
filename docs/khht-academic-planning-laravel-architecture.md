# Thiết kế Module KẾ HOẠCH HỌC TẬP (KHHT) — SIS + LMS (Laravel 11+)

> Phạm vi: phân tích và thiết kế kiến trúc production-ready cho module KHHT của Trường Cao đẳng Quốc tế VABIS theo hướng API-first.

## Giả định thiết kế
1. Mỗi sinh viên thuộc đúng **một chương trình đào tạo chính** tại một thời điểm (chuyển ngành sẽ sinh bản ghi lịch sử).
2. Một năm có tối đa 3 kỳ chuẩn (`SPRING`, `SUMMER`, `FALL`) và có thể cấu hình kỳ đặc biệt.
3. Quy tắc tải tối đa theo kỳ mặc định là 24 tín chỉ, có thể override theo đối tượng SV.
4. Dữ liệu điểm/GPA, lớp mở, đăng ký môn chính thức được đồng bộ từ các module SIS hiện có thông qua integration API/event.
5. KHHT mang tính định hướng; đăng ký học phần chính thức vẫn do module Enrollment quyết định.

---

## Phần 1. Phân tích nghiệp vụ

### 1.1 Tóm tắt bản chất module
KHHT là lớp “orchestration” giữa **CTĐT chuẩn** và **lộ trình cá nhân của SV**. Module không chỉ lưu kế hoạch môn học tương lai, mà còn là công cụ ra quyết định cho CVHT/P.ĐT thông qua:
- kiểm tra điều kiện tiên quyết theo đồ thị môn học,
- mô phỏng tải học tập,
- dự báo tiến độ tốt nghiệp,
- giám sát lệch giữa kế hoạch và thực tế,
- workflow duyệt đa vai trò.

### 1.2 Actor
- **Sinh viên (SV)**: tạo/chỉnh sửa phương án KHHT, nộp duyệt, theo dõi cảnh báo.
- **Cố vấn học tập (CVHT)**: phản biện kế hoạch, ghi chú theo kỳ, duyệt hoặc trả sửa.
- **Phòng Đào tạo (P.ĐT)**: phê duyệt ngoại lệ, quản trị quy tắc và giám sát toàn trường.
- **Ban giám hiệu (BGH)**: xem dashboard tổng hợp, dự báo nhu cầu mở lớp/chậm tiến độ.
- **Hệ SIS-CTĐT integration**: phát sự kiện thay đổi CTĐT/điều kiện tiên quyết.
- **Hệ Enrollment**: cung cấp trạng thái lớp mở/chỉ tiêu phục vụ gợi ý đăng ký.
- **Notification service**: gửi push/email/in-app theo workflow.

### 1.3 Use case chính
1. SV tạo tối đa 3 phương án KHHT, chọn 1 phương án chính để submit.
2. SV kéo-thả học phần vào kỳ tương lai và nhận cảnh báo real-time.
3. Hệ thống tự đánh giá tính hợp lệ: tiên quyết, quá tải TC, trùng lịch dự kiến, môn F chưa retake.
4. CVHT nhận thông báo khi plan thay đổi, thêm ghi chú, approve hoặc request revision.
5. P.ĐT chỉ tham gia phê duyệt cuối cho ca ngoại lệ (quá tải, bypass tiên quyết có điều kiện).
6. Sau mỗi kỳ, hệ thống auto-reconcile kế hoạch với kết quả thực tế và gợi ý điều chỉnh.
7. Dashboard phân tích rủi ro chậm tiến độ ở cấp SV, ngành, khóa.

### 1.4 Quy tắc nghiệp vụ quan trọng
- Mỗi SV có tối đa **03 plan draft/scenario** đồng thời.
- Chỉ **01 plan active_primary** tại một thời điểm.
- Chỉ plan ở trạng thái `DRAFT`/`REVISION_REQUIRED` mới được chỉnh sửa bởi SV.
- Submit plan yêu cầu:
  - không vi phạm tiên quyết “cứng”,
  - không vượt max tín chỉ kỳ nếu không có cờ ngoại lệ,
  - có phương án xử lý môn F bắt buộc.
- Duyệt 3 bước: `SUBMITTED -> ADVISOR_APPROVED -> REGISTRAR_APPROVED` (khi cần), hoặc `REVISION_REQUIRED`.
- Khi CTĐT đổi version, plan bị tác động phải gắn cờ `needs_rebase` và phát thông báo.
- Quy tắc conflict thời khóa biểu là “best effort” theo lớp mở dự kiến; quyết định cuối nằm ở Enrollment.

---

## Phần 2. Thiết kế domain model

### 2.1 Thực thể chính
1. **AcademicPlan**: hồ sơ kế hoạch cấp SV theo scenario.
2. **AcademicPlanTerm**: khối kế hoạch theo từng học kỳ trong plan.
3. **AcademicPlanCourseItem**: học phần cụ thể trong từng kỳ kế hoạch.
4. **PlanReview**: lịch sử duyệt/trả sửa của CVHT/P.ĐT.
5. **PlanAdvisorNote**: ghi chú theo kỳ/môn từ CVHT.
6. **PlanValidationIssue**: lỗi/cảnh báo phát hiện khi validate plan.
7. **PlanProjectionSnapshot**: snapshot dự báo tốt nghiệp và tải học tập.
8. **ProgramCurriculumVersionRef**: tham chiếu version CTĐT áp dụng cho plan.
9. **CreditRecognitionRef**: kết quả công nhận tương đương ảnh hưởng KHHT.
10. **RetakeSuggestion**: đề xuất học lại cho môn F/FX.
11. **PlanImpactEvent**: tác động từ thay đổi CTĐT/điểm/đăng ký môn.

### 2.2 Quan hệ giữa thực thể
- `AcademicPlan 1 - n AcademicPlanTerm`
- `AcademicPlanTerm 1 - n AcademicPlanCourseItem`
- `AcademicPlan 1 - n PlanReview`
- `AcademicPlan 1 - n PlanValidationIssue`
- `AcademicPlan 1 - n PlanProjectionSnapshot`
- `AcademicPlanCourseItem 1 - n PlanValidationIssue`
- `AcademicPlan 1 - n PlanImpactEvent`
- `AcademicPlanCourseItem` liên kết `course_catalog_id` (từ SIS curriculum service).

### 2.3 Trạng thái kế hoạch (PlanStatus)
- `DRAFT`
- `SUBMITTED`
- `ADVISOR_REVIEWING`
- `REVISION_REQUIRED`
- `ADVISOR_APPROVED`
- `REGISTRAR_REVIEWING`
- `REGISTRAR_APPROVED`
- `ARCHIVED`

### 2.4 Trạng thái học phần trong kế hoạch (PlanCourseStatus)
- `PLANNED` (đã xếp kế hoạch)
- `IN_PROGRESS` (đang học thực tế)
- `PASSED`
- `FAILED`
- `RETAKE_REQUIRED`
- `EXEMPTED` (được công nhận tương đương)
- `CANCELLED`

---

## Phần 3. Thiết kế database

### 3.1 Danh sách bảng
1. `academic_plans`
2. `academic_plan_terms`
3. `academic_plan_course_items`
4. `academic_plan_reviews`
5. `academic_plan_advisor_notes`
6. `academic_plan_validation_issues`
7. `academic_plan_projection_snapshots`
8. `academic_plan_impact_events`
9. `academic_plan_retake_suggestions`
10. `academic_plan_workflow_histories`
11. `academic_plan_print_exports` (tùy chọn cho one-page print cache)

### 3.2 Trường chính đề xuất

#### `academic_plans`
- `id` (uuid/bigint)
- `student_id` (FK)
- `program_id`
- `curriculum_version_id`
- `scenario_no` (1..3)
- `is_primary` (bool)
- `status` (enum)
- `submitted_at`, `advisor_approved_at`, `registrar_approved_at`
- `needs_rebase` (bool)
- `graduation_forecast_term_id` (nullable)
- `risk_level` (`LOW|MEDIUM|HIGH`)
- `created_by`, `updated_by`
- timestamps, softDeletes

#### `academic_plan_terms`
- `id`
- `academic_plan_id` (FK)
- `term_id` (FK học kỳ chuẩn)
- `planned_credits`
- `planned_weekly_hours`
- `load_level` (`GREEN|YELLOW|RED`)
- `is_locked` (bool)
- timestamps

#### `academic_plan_course_items`
- `id`
- `academic_plan_term_id` (FK)
- `course_id`
- `course_code_snapshot`, `course_name_snapshot`
- `credits_snapshot`
- `course_type_snapshot` (`THEORY|PRACTICE|ONLINE|INTERNSHIP|CAPSTONE`)
- `status` (enum PlanCourseStatus)
- `source` (`MANUAL|AUTO_RETAKE|AUTO_SYNC|EQUIVALENT_CREDIT`)
- `is_prereq_satisfied` (bool)
- `has_schedule_conflict` (bool)
- `priority_order` (int)
- timestamps

#### `academic_plan_reviews`
- `id`
- `academic_plan_id`
- `reviewer_id`
- `reviewer_role` (`ADVISOR|REGISTRAR`)
- `decision` (`APPROVE|REQUEST_REVISION|ESCALATE_EXCEPTION`)
- `comment`
- `reviewed_at`

#### `academic_plan_validation_issues`
- `id`
- `academic_plan_id`
- `academic_plan_course_item_id` nullable
- `issue_type` (`PREREQ|CREDIT_OVERLOAD|SCHEDULE_CONFLICT|RETAKE_MISSING|CURRICULUM_CHANGED`)
- `severity` (`INFO|WARNING|ERROR`)
- `message`
- `payload_json`
- `resolved_at`

#### `academic_plan_projection_snapshots`
- `id`
- `academic_plan_id`
- `snapshot_at`
- `remaining_credits`
- `estimated_graduation_term_id`
- `estimated_delay_terms`
- `scenario` (`CURRENT_LOAD|INCREASED_LOAD`)
- `projection_payload_json`

### 3.3 Quan hệ khóa ngoại
- `academic_plans.student_id -> students.id`
- `academic_plans.program_id -> programs.id`
- `academic_plans.curriculum_version_id -> curriculum_versions.id`
- `academic_plan_terms.academic_plan_id -> academic_plans.id`
- `academic_plan_terms.term_id -> terms.id`
- `academic_plan_course_items.academic_plan_term_id -> academic_plan_terms.id`
- `academic_plan_course_items.course_id -> courses.id`
- `academic_plan_reviews.academic_plan_id -> academic_plans.id`
- `academic_plan_validation_issues.academic_plan_id -> academic_plans.id`

### 3.4 Index cần có
- `academic_plans(student_id, status)`
- unique partial: `academic_plans(student_id, is_primary=true)`
- unique: `academic_plans(student_id, scenario_no)`
- `academic_plan_terms(academic_plan_id, term_id)` unique
- `academic_plan_course_items(academic_plan_term_id, course_id)` unique
- `academic_plan_validation_issues(academic_plan_id, severity, resolved_at)`
- `academic_plan_projection_snapshots(academic_plan_id, snapshot_at desc)`
- `academic_plan_impact_events(event_type, processed_at)`

### 3.5 Enum/status đề xuất
- `PlanStatus`, `PlanCourseStatus`, `ReviewDecision`, `IssueType`, `IssueSeverity`, `LoadLevel`, `RiskLevel`.
- Đặt tại `app/Enums/AcademicPlan/*` để type-safe xuyên suốt service/request/resource.

---

## Phần 4. Thiết kế API

> Base prefix: `/api/v1/academic-plans`

### 4.1 Endpoint cho Student
- `GET /me/plans` — danh sách phương án KHHT.
- `POST /me/plans` — tạo phương án mới (max 3).
- `GET /me/plans/{plan}` — chi tiết plan + validation summary.
- `PATCH /me/plans/{plan}` — cập nhật metadata plan.
- `POST /me/plans/{plan}/set-primary` — chọn phương án chính.
- `POST /me/plans/{plan}/terms/{term}/items` — thêm môn vào kỳ.
- `PATCH /me/plans/{plan}/terms/{term}/items/{item}` — đổi kỳ/độ ưu tiên/trạng thái.
- `DELETE /me/plans/{plan}/terms/{term}/items/{item}` — gỡ môn khỏi kế hoạch.
- `POST /me/plans/{plan}/validate` — chạy kiểm tra real-time.
- `POST /me/plans/{plan}/submit` — nộp duyệt.
- `GET /me/plans/{plan}/projection` — dự báo tốt nghiệp/tải học tập.
- `GET /me/plans/{plan}/compare/{otherPlan}` — so sánh 2 phương án.
- `GET /me/plans/{plan}/print` — dữ liệu in 1 trang.

### 4.2 Endpoint cho CVHT
- `GET /advisor/plans/pending` — danh sách plan chờ duyệt.
- `GET /advisor/plans/{plan}` — xem chi tiết + lịch sử.
- `POST /advisor/plans/{plan}/notes` — ghi chú theo kỳ/môn.
- `POST /advisor/plans/{plan}/decision` — approve/request revision/escalate.

### 4.3 Endpoint cho P.ĐT
- `GET /registrar/plans/exceptions` — queue ngoại lệ.
- `POST /registrar/plans/{plan}/decision` — phê duyệt cuối hoặc trả sửa.
- `POST /registrar/plans/{plan}/override` — duyệt quá tải/bypass có điều kiện.
- `POST /registrar/credit-recognitions` — nhập quyết định công nhận tương đương.

### 4.4 Endpoint cho Dashboard
- `GET /analytics/overview` — tỷ lệ có KHHT, tỷ lệ duyệt.
- `GET /analytics/risk` — heatmap chậm tiến độ theo ngành/khóa.
- `GET /analytics/course-demand-next-term` — dự báo nhu cầu lớp kỳ tới.
- `GET /analytics/cohort-benchmark` — benchmark ẩn danh theo khóa/ngành.

### 4.5 Endpoint tích hợp SIS / Enrollment / CTĐT
- `POST /integrations/curriculum-changes` — webhook thay đổi CTĐT.
- `POST /integrations/grade-updates` — webhook kết quả học tập.
- `POST /integrations/enrollment-open-courses` — lớp mở/chỉ tiêu.
- `POST /integrations/credit-recognition-updates` — cập nhật công nhận môn.

---

## Phần 5. Thiết kế kiến trúc Laravel 11+

### 5.1 Routes
- `routes/api.php` tách theo nhóm middleware + prefix:
  - `auth:sanctum` + `role:student`
  - `role:advisor`
  - `role:registrar`
  - `permission:dashboard.view`
  - `middleware:verify.integration.signature` cho webhook.

### 5.2 Controllers (mỏng)
- `StudentAcademicPlanController`
- `StudentPlanItemController`
- `StudentPlanSubmissionController`
- `AdvisorPlanReviewController`
- `RegistrarPlanDecisionController`
- `AcademicPlanAnalyticsController`
- `AcademicPlanIntegrationController`

Controller chỉ làm:
1. nhận request đã validate,
2. gọi service,
3. trả `JsonResource` + response envelope thống nhất.

### 5.3 Requests
- `CreateAcademicPlanRequest`
- `UpdateAcademicPlanRequest`
- `AddPlanCourseItemRequest`
- `UpdatePlanCourseItemRequest`
- `SubmitAcademicPlanRequest`
- `AdvisorDecisionRequest`
- `RegistrarDecisionRequest`
- `CurriculumChangeWebhookRequest`
- `GradeUpdateWebhookRequest`

### 5.4 Services (core business)
- `AcademicPlanCommandService` (create/update/submit/primary)
- `PlanValidationService` (prereq/load/conflict/rules)
- `PlanProjectionService` (graduation forecast, delay estimate)
- `PlanWorkflowService` (state transition + guard)
- `PlanSyncService` (đồng bộ grade/curriculum/enrollment)
- `PlanRetakeSuggestionService`
- `PlanAnalyticsService`
- `PlanNotificationService`

### 5.5 Repositories (khi scale lớn)
- `AcademicPlanRepository`
- `PlanItemRepository`
- `PlanProjectionRepository`
- `PlanAnalyticsReadRepository` (query tối ưu dashboard)

### 5.6 Jobs
- `ProcessCurriculumChangeImpactJob`
- `RecalculateStudentPlanProjectionJob`
- `BulkRecalculatePlanProjectionByProgramJob`
- `DispatchPlanNotificationJob`
- `RebuildPlanValidationCacheJob`

### 5.7 Events / Listeners
- Events:
  - `AcademicPlanSubmitted`
  - `AcademicPlanRevisionRequested`
  - `AcademicPlanApprovedByAdvisor`
  - `AcademicPlanApprovedByRegistrar`
  - `AcademicPlanImpactedByCurriculumChange`
  - `StudentGradePosted`
- Listeners:
  - `QueueAdvisorNotificationListener`
  - `QueueStudentNotificationListener`
  - `TriggerProjectionRecalculationListener`
  - `CreateAuditTrailListener`

### 5.8 Policies / Middleware
- `AcademicPlanPolicy`: view/update/submit/review/override theo owner + role + trạng thái.
- Middleware:
  - `EnsurePlanEditable`
  - `EnsureAdvisorOwnsStudent`
  - `VerifyIntegrationSignature`

### 5.9 Resources
- `AcademicPlanResource`
- `AcademicPlanDetailResource`
- `AcademicPlanTermResource`
- `AcademicPlanCourseItemResource`
- `PlanValidationIssueResource`
- `PlanProjectionResource`
- `PlanAnalyticsOverviewResource`

---

## Phần 6. Đề xuất chia phase triển khai

### Phase 1 — Core usable (MVP vận hành)
- CRUD plan + plan items + max 3 scenarios.
- Validate tiên quyết + quá tải TC.
- Submit plan + CVHT review cơ bản.
- Projection tốt nghiệp baseline.
- API cho web/mobile + in one-page.

### Phase 2 — Workflow + Analytics
- Workflow đầy đủ CVHT + P.ĐT ngoại lệ.
- Auto reconcile theo điểm thực tế và retake suggestion.
- Dashboard risk/cohort/course-demand.
- Integration webhook CTĐT/Enrollment/Grade.
- Audit log + notification queue hóa.

### Phase 3 — AI + Optimization
- Recommendation engine tối ưu thứ tự học phần.
- Mô hình dự báo xác suất tốt nghiệp đúng hạn.
- What-if simulation (tăng/giảm tải, đổi ca học, ưu tiên GPA).
- A/B testing chiến lược gợi ý.

---

## Phần 7. Danh sách file Laravel đề xuất tạo

### 7.1 Routes
- `routes/api/academic_plan_student.php`
- `routes/api/academic_plan_advisor.php`
- `routes/api/academic_plan_registrar.php`
- `routes/api/academic_plan_analytics.php`
- `routes/api/academic_plan_integrations.php`

### 7.2 Models
- `app/Models/AcademicPlan.php`
- `app/Models/AcademicPlanTerm.php`
- `app/Models/AcademicPlanCourseItem.php`
- `app/Models/AcademicPlanReview.php`
- `app/Models/AcademicPlanValidationIssue.php`
- `app/Models/AcademicPlanProjectionSnapshot.php`
- `app/Models/AcademicPlanImpactEvent.php`

### 7.3 Enums
- `app/Enums/AcademicPlan/PlanStatus.php`
- `app/Enums/AcademicPlan/PlanCourseStatus.php`
- `app/Enums/AcademicPlan/ReviewDecision.php`
- `app/Enums/AcademicPlan/IssueSeverity.php`
- `app/Enums/AcademicPlan/IssueType.php`
- `app/Enums/AcademicPlan/LoadLevel.php`
- `app/Enums/AcademicPlan/RiskLevel.php`

### 7.4 Controllers
- `app/Http/Controllers/Api/V1/AcademicPlan/StudentAcademicPlanController.php`
- `app/Http/Controllers/Api/V1/AcademicPlan/StudentPlanItemController.php`
- `app/Http/Controllers/Api/V1/AcademicPlan/StudentPlanSubmissionController.php`
- `app/Http/Controllers/Api/V1/AcademicPlan/AdvisorPlanReviewController.php`
- `app/Http/Controllers/Api/V1/AcademicPlan/RegistrarPlanDecisionController.php`
- `app/Http/Controllers/Api/V1/AcademicPlan/AcademicPlanAnalyticsController.php`
- `app/Http/Controllers/Api/V1/AcademicPlan/AcademicPlanIntegrationController.php`

### 7.5 Requests
- `app/Http/Requests/AcademicPlan/CreateAcademicPlanRequest.php`
- `app/Http/Requests/AcademicPlan/UpdateAcademicPlanRequest.php`
- `app/Http/Requests/AcademicPlan/AddPlanCourseItemRequest.php`
- `app/Http/Requests/AcademicPlan/UpdatePlanCourseItemRequest.php`
- `app/Http/Requests/AcademicPlan/SubmitAcademicPlanRequest.php`
- `app/Http/Requests/AcademicPlan/AdvisorDecisionRequest.php`
- `app/Http/Requests/AcademicPlan/RegistrarDecisionRequest.php`
- `app/Http/Requests/AcademicPlan/CurriculumChangeWebhookRequest.php`
- `app/Http/Requests/AcademicPlan/GradeUpdateWebhookRequest.php`

### 7.6 Services
- `app/Services/AcademicPlan/AcademicPlanCommandService.php`
- `app/Services/AcademicPlan/PlanValidationService.php`
- `app/Services/AcademicPlan/PlanProjectionService.php`
- `app/Services/AcademicPlan/PlanWorkflowService.php`
- `app/Services/AcademicPlan/PlanSyncService.php`
- `app/Services/AcademicPlan/PlanRetakeSuggestionService.php`
- `app/Services/AcademicPlan/PlanAnalyticsService.php`
- `app/Services/AcademicPlan/PlanNotificationService.php`

### 7.7 Repositories
- `app/Repositories/AcademicPlan/AcademicPlanRepository.php`
- `app/Repositories/AcademicPlan/PlanItemRepository.php`
- `app/Repositories/AcademicPlan/PlanAnalyticsReadRepository.php`

### 7.8 Jobs / Events / Listeners
- `app/Jobs/AcademicPlan/*.php`
- `app/Events/AcademicPlan/*.php`
- `app/Listeners/AcademicPlan/*.php`

### 7.9 Policies / Resources
- `app/Policies/AcademicPlanPolicy.php`
- `app/Http/Resources/AcademicPlan/*.php`

### 7.10 Migrations
- `database/migrations/*_create_academic_plans_table.php`
- `database/migrations/*_create_academic_plan_terms_table.php`
- `database/migrations/*_create_academic_plan_course_items_table.php`
- `database/migrations/*_create_academic_plan_reviews_table.php`
- `database/migrations/*_create_academic_plan_validation_issues_table.php`
- `database/migrations/*_create_academic_plan_projection_snapshots_table.php`
- `database/migrations/*_create_academic_plan_impact_events_table.php`

---

## Khuyến nghị trước khi sang bước code
1. Chốt chính sách workflow ngoại lệ (khi nào bắt buộc P.ĐT duyệt).
2. Chốt chuẩn dữ liệu term/calendar dùng chung giữa SIS và Enrollment.
3. Chốt “hard prereq” vs “soft prereq” theo từng ngành.
4. Chốt ngưỡng risk dashboard theo từng chương trình đào tạo.
5. Chốt cơ chế idempotency cho webhook integration.
