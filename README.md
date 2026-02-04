# SIS Demo Laravel 11 (MVP)

Demo hệ thống SIS theo chuẩn A→O với giao diện tiếng Việt, API JSON và dữ liệu mẫu.

## Chạy local bằng Docker Compose

```bash
docker compose up -d
composer install
php artisan migrate --seed
php artisan demo:chay
```

Sau đó truy cập `http://localhost:8000/quan-tri` để xem dashboard.

## Thông tin đăng nhập demo

- Email: `admin@demo.local`
- Mật khẩu: `password`

## Ghi chú

- Dữ liệu mẫu được tạo trong `DatabaseSeeder`.
- Bộ xếp TKB sử dụng solver greedy demo (không gọi external solver).
