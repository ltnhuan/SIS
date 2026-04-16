# VABIS Sprint Execution Status

## Trạng thái hiện tại
- ✅ Sprint 0 / Prompt 001 (hạ tầng cục bộ cơ bản) đã scaffold:
  - `docker-compose.yml`
  - `.env.example`
  - `app/Traits/ApiResponse.php`
  - `app/Exceptions/Handler.php`
  - `bootstrap/app.php` (rate limit)
  - `.github/workflows/ci.yml`

## TODO chạy tiếp theo
1. Prompt-002: users migration + model + enum + tests
2. Prompt-003: core SIS schema
3. Prompt-004: students/classes
4. Prompt-005: courses/curriculum
5. Prompt-006: LMS core

> Ghi chú: do repository hiện chưa chứa full Laravel skeleton (`composer.json`, artisan, vendor/...), các bước còn lại cần chạy trong môi trường Laravel đầy đủ.
