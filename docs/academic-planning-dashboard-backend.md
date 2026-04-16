# Academic Planning Dashboard Backend (KHHT)

## 1) Công thức / logic nghiệp vụ

### 1.1 Dự báo ngày tốt nghiệp sớm nhất
- `remaining_credits = curriculum_total_credits - max(passed_credits, planned_credits)`
- `required_semesters = ceil(remaining_credits / max_credits_per_semester)`
- `estimated_graduation_date = next_semester_start + required_semesters * 4 months`

### 1.2 So sánh tiến trình kế hoạch vs thực tế
- Planned series: tổng tín chỉ theo kỳ từ `study_plan_items`.
- Actual series: tổng tín chỉ đã đạt theo kỳ từ `course_results`.
- Frontend vẽ 2 line chart + có thể thêm baseline line.

### 1.3 Phân tích tải học tập từng học kỳ
- `total_credits`, `total_courses`, `estimated_weekly_hours = total_credits * 3`.
- Risk color:
  - xanh: `< max - 3`
  - vàng: `>= max - 3 && <= max`
  - đỏ: `> max`

### 1.4 Phát hiện nguy cơ chậm tiến độ
- Cờ rủi ro:
  - môn F chưa đưa vào kế hoạch học lại,
  - vi phạm tiên quyết,
  - quá tải tín chỉ.
- Xếp hạng risk:
  - `low` / `medium` / `high`.

## 2) Cấu trúc service
- `GraduationForecastService`
- `ProgressComparisonService`
- `SemesterWorkloadAnalyticsService`
- `ProgressRiskDetectionService`
- `AcademicPlanningDashboardService` (orchestrator + cache + snapshot + queue dispatch)

## 3) Snapshot
Bảng `academic_planning_dashboard_snapshots` lưu payload forecast/progress/workload/risk theo ngày để phục vụ dashboard nhanh và lịch sử.

## 4) Endpoint API
- `GET /api/v1/academic-planning/dashboard/student`
- `POST /api/v1/academic-planning/dashboard/student/recompute`
- `GET /api/v1/academic-planning/dashboard/advisor`
- `GET /api/v1/academic-planning/dashboard/registrar`

## 5) JSON chart payload
Payload chuẩn gồm các khối:
- `forecast`
- `progress` (planned_series, actual_series)
- `workload` (series)
- `risk`

## 6) Tối ưu performance khi quy mô lớn
1. Cache dashboard per-student (TTL 10 phút, force refresh).
2. Snapshot theo ngày để giảm truy vấn tổng hợp nặng.
3. Queue job `RecomputeAcademicPlanningDashboardSnapshotJob` để tái tính toán bất đồng bộ.
4. Index theo `student_id`, `snapshot_date`, `study_plan_version_id`.
5. Gom truy vấn dạng aggregate theo kỳ thay vì query từng môn.
