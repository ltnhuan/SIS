# Academic Planning Validation Engine (KHHT) - Backend Design & Response Schema

## 1) Response Schema chuẩn

```json
{
  "success": true,
  "message": "Validation thành công.",
  "data": {
    "can_save": true,
    "outcome": "valid",
    "summary": {
      "total_issues": 0,
      "blocking_count": 0,
      "warning_count": 0,
      "info_count": 0
    },
    "issues": [
      {
        "code": "semester.credit_near_limit",
        "severity": "warning",
        "message": "Tín chỉ học kỳ đã gần ngưỡng tối đa.",
        "meta": {
          "planned_credits_after_add": 22,
          "max_credits": 24
        }
      }
    ]
  }
}
```

## 2) Rule severity
- `info`: thông tin không ảnh hưởng lưu.
- `warning`: cho phép lưu nhưng cần hiển thị cảnh báo inline.
- `blocking`: chặn lưu.

## 3) Mở rộng rule engine
Engine dùng mô hình `RuleValidatorInterface` + danh sách validator nhỏ, có thể cắm thêm rule mới mà không sửa controller.

## 4) Mapping rule đã có trong phase 1
1. `course.already_completed`
2. `course.currently_in_progress`
3. `prerequisite.not_satisfied`
4. `prerequisite.invalid_semester_order`
5. `semester.credit_near_limit`
6. `semester.credit_overload`
7. `schedule.conflict_detected`
8. `graduation.forecast_risk`
