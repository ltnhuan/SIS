# SIS + LMS Laravel Delivery Playbook (Vietnam VET/College)

Tài liệu này chuẩn hóa cách phân tích, thiết kế và triển khai module Laravel production-grade cho hệ thống giáo dục nghề nghiệp/cao đẳng tại Việt Nam.

## 1) Mục tiêu
- Chuẩn hóa cách triển khai module theo kiến trúc Laravel 11+.
- Bảo đảm controller mỏng, business logic tách service layer.
- Thiết kế phù hợp tích hợp SIS, LMS, mobile app, workflow duyệt và báo cáo quản trị.

## 2) Quy trình thực hiện bắt buộc
Mỗi bài toán phải đi theo thứ tự:
1. **Phân tích nghiệp vụ**.
2. **Xác định actor và use case**.
3. **Thiết kế database**.
4. **Thiết kế kiến trúc Laravel**.
5. **Thiết kế API**.
6. **Sinh code từng file (chạy được)**.
7. **Hướng dẫn chạy và test**.
8. **Đề xuất mở rộng production**.

Khi thiếu dữ liệu, phải ghi rõ giả định.

## 3) Chuẩn kiến trúc Laravel
- Laravel 11+.
- Tách lớp rõ ràng:
  - `routes/`
  - `app/Http/Controllers/`
  - `app/Http/Requests/`
  - `app/Services/`
  - `app/Repositories/` (khi cần)
  - `app/Models/`
  - `app/Policies/` hoặc middleware
  - `app/Jobs/`
  - `app/Events/`, `app/Listeners/`
  - `app/Http/Resources/`
- Không đặt business logic trực tiếp trong Controller.
- Validation dùng Form Request.
- API trả JSON nhất quán (nên thống nhất qua resource + response wrapper).
- Trạng thái/role/workflow state dùng Enum hoặc config hằng.
- Xem xét Queue, Notification, Audit log, Scheduler cho các luồng bất đồng bộ.

## 4) Khung output cho từng module
Mỗi module nên có đầy đủ:
1. **Business summary**.
2. **Use case matrix (actor -> action -> outcome)**.
3. **ERD tóm tắt + danh sách bảng chính**.
4. **Danh sách endpoint** (method, path, quyền, request/response).
5. **Danh sách file code cần tạo/sửa**.
6. **Migrations + seed cơ bản**.
7. **Luồng xử lý chính (service orchestration)**.
8. **Checklist kiểm thử** (feature test + integration test).
9. **Vận hành production** (queue worker, scheduler, monitoring).

## 5) Tiêu chuẩn chất lượng code
- Tên file, class, method rõ nghĩa theo domain giáo dục.
- Ưu tiên idempotency cho API tạo dữ liệu quan trọng.
- Bổ sung transaction cho luồng ghi nhiều bảng.
- Chuẩn hóa lỗi nghiệp vụ và mã lỗi.
- Log có ngữ cảnh (user_id, tenant/campus_id, correlation_id nếu có).

## 6) Gợi ý module cốt lõi SIS + LMS
- Tuyển sinh & nhập học.
- Chương trình đào tạo, khung môn học, mở lớp học phần.
- Đăng ký học phần & thời khóa biểu.
- Điểm danh, quá trình học tập, điểm thành phần & điểm tổng kết.
- Học phí/công nợ.
- Cổng giảng viên/cố vấn học tập.
- LMS assignment/quiz/grade sync.
- Báo cáo quản trị & dashboard điều hành.

## 7) Quy ước response JSON gợi ý
```json
{
  "success": true,
  "message": "OK",
  "data": {},
  "meta": {
    "request_id": "...",
    "timestamp": "..."
  }
}
```

## 8) Vận hành production
- Queue: Redis + Horizon.
- Cache: Redis cho danh mục và cấu hình tần suất cao.
- Scheduler: đồng bộ LMS, chốt điểm, nhắc việc duyệt.
- Audit log: theo dõi thay đổi điểm số, trạng thái duyệt, phân quyền.
- Backup/DR: backup DB định kỳ + kiểm thử restore.

---
Tài liệu này là baseline để đội dev mở rộng thành blueprint chi tiết cho từng module nghiệp vụ.
