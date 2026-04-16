# VABIS — Claude Code Prompt Runbook (Laravel 11, 2026)

Tài liệu này chuẩn hóa cách chạy chuỗi prompt theo sprint cho dự án SIS + LMS của VABIS, dựa trên nội dung bạn cung cấp.

## Nguyên tắc chạy
1. Chạy **đúng thứ tự prompt**.
2. Sau mỗi prompt có migration: chạy `php artisan migrate`.
3. Sau mỗi prompt có test: chạy `php artisan test`.
4. Không chạy prompt tiếp theo nếu test đang fail.
5. Mỗi prompt cần output theo chuẩn production:
   - controller mỏng,
   - validate bằng Form Request,
   - business logic trong Service,
   - policy/middleware,
   - event/job/notification khi phù hợp.

---

## Sprint 0 — Nền tảng

### PROMPT-001 | Docker + Laravel Setup
- Docker Compose: PostgreSQL 16, Redis 7, MinIO, Mailpit.
- Cài Laravel 11 + package: Sanctum, Horizon, Reverb.
- Thiết lập `.env` cho PostgreSQL/Redis/S3(MinIO)/SMTP(Mailpit).
- Tạo thư mục chuẩn (`Services`, `Jobs`, `Events`, `Listeners`, `Traits`, `Enums`).
- Tạo `ApiResponse` trait, JSON exception handling, rate limit, CI workflow.

### PROMPT-002 | Migration & Model: users
- Thiết kế bảng `users` cho 7 role.
- Enum `UserRole`, `UserStatus`.
- User model helper/scopes/factory/seeder.
- Unit test `UserModelTest`.

### PROMPT-003 | Migration: Bảng lõi SIS
- Migrations: `academic_years`, `semesters`, `faculties`, `departments`, `rooms`.
- Models + relationships.
- Seeder dữ liệu năm học/khoa/bộ môn/phòng.

### PROMPT-004 | Migration & Model: students, classes
- Migrations: `classes`, `students`, `student_parents`.
- Models + accessors/scopes.
- Factory + seeder sinh viên theo lớp.

### PROMPT-005 | Migration & Model: courses, curriculum
- Migrations: `courses`, `curriculums`, `curriculum_courses`, `teaching_assignments`.
- Models + relationship + seeder CTĐT.

### PROMPT-006 | Migration & Model: LMS Core
- Migrations: `lms_courses`, `lms_lessons`, `lms_progress`, `lms_assignments`, `lms_submissions`.
- Models + seeder khóa LMS.

---

## Sprint 1 — SIS cốt lõi

### PROMPT-010 | Auth API + RBAC
- Login/logout/me/forgot/reset/change-password.
- Middleware `EnsureRole` + route groups theo role.
- Feature tests cho auth + role middleware.

### PROMPT-011 | Tuyển sinh: Form + xét duyệt
- Module `applications` + `application_logs`.
- `AdmissionService` + workflow submit/review/stats.
- Jobs/events/listeners + test suite.

### PROMPT-012 | Nhập học tự động
- `EnrollStudentJob` chuyển accepted application -> student.
- Idempotent + transaction + gửi email chào mừng.
- API trigger enroll + tests.

### PROMPT-015 | Điểm danh QR + GPS
- Migrations attendance sessions/records.
- QR token + verify GPS (Haversine < 50m).
- Observer tạo session tự động theo lịch.
- Command expire QR mỗi phút.

### PROMPT-018 | Thời khóa biểu
- Migrations schedule/exam schedule.
- `ScheduleService`: conflict detection, suggest slot, publish, move session.
- API staff/sv/gv + tests.

### PROMPT-019 | Học phí + thanh toán
- Migrations fee/payment/scholarship.
- PaymentGateway interface + adapters.
- `FeeService`: generate invoice, webhook payment, scholarship apply.
- Jobs/scheduler/events + tests.

### PROMPT-020 | LMS video upload + nội dung
- `LmsCourseService`: access/progress.
- Upload video pipeline + transcode job + thumbnail job.
- Presigned URL cho video/PDF.
- Feature tests.

---

## Sprint 2 — Mở rộng

### PROMPT-030 | Nhân sự: hồ sơ + lương
- Migrations staffs/contracts/leave/payroll.
- `PayrollService` tính BHXH + thuế TNCN.
- Payslip PDF lên MinIO.
- Authorization payroll + tests.

### PROMPT-033 | Thông báo đa kênh (FCM + ZNS + Email)
- Migrations `device_tokens`, `notifications`.
- `NotificationService`, `FcmService`, `ZnsService`.
- API thông báo + unread count + realtime broadcast Reverb.
- Feature tests.

---

## Checklist vận hành mỗi prompt
1. Chạy migration/test local.
2. Kiểm tra queue worker + scheduler.
3. Kiểm tra policy/middleware cho role dữ liệu nhạy cảm.
4. Kiểm tra idempotency cho webhook/job.
5. Cập nhật docs API (request/response mẫu) trước khi merge.
