<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIS Demo - Quản trị</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f6f8; margin: 0; }
        header { background: #1f2937; color: #fff; padding: 16px 24px; }
        nav { background: #111827; padding: 12px 24px; }
        nav a { color: #d1d5db; margin-right: 12px; text-decoration: none; }
        nav a:hover { color: #fff; }
        .container { padding: 24px; }
        .card { background: #fff; border-radius: 8px; padding: 16px; margin-bottom: 16px; box-shadow: 0 1px 2px rgba(0,0,0,0.06); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px 12px; border-bottom: 1px solid #e5e7eb; text-align: left; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; background: #e0f2fe; color: #0369a1; font-size: 12px; }
        .actions { display: flex; gap: 8px; flex-wrap: wrap; }
        button { background: #2563eb; color: #fff; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer; }
        button.secondary { background: #6b7280; }
        .status { color: #065f46; font-weight: bold; }
    </style>
</head>
<body>
<header>
    <h1>SIS Demo - Quản trị</h1>
</header>
<nav>
    <a href="/quan-tri">Tổng quan</a>
    <a href="/quan-tri/to-chuc">Tổ chức & Danh mục</a>
    <a href="/quan-tri/sinh-vien">Sinh viên</a>
    <a href="/quan-tri/giang-vien">Giảng viên</a>
    <a href="/quan-tri/ctdt">CTĐT (Phiên bản)</a>
    <a href="/quan-tri/ke-hoach">Kế hoạch theo khóa</a>
    <a href="/quan-tri/mo-lop">Mở lớp & Đăng ký</a>
    <a href="/quan-tri/apps">App SV/GV/PH</a>
    <a href="/quan-tri/xep-tkb">Xếp TKB</a>
    <a href="/quan-tri/diem-danh">Điểm danh</a>
    <a href="/quan-tri/diem-so">Điểm số & GPA</a>
    <a href="/quan-tri/canh-bao">Cảnh báo</a>
    <a href="/quan-tri/tickets">Ticket/Đơn từ</a>
    <a href="/quan-tri/thong-bao">Thông báo</a>
    <a href="/quan-tri/tot-nghiep">Tốt nghiệp</a>
</nav>
<div class="container">
    @if(session('status'))
        <div class="card">
            <span class="status">{{ session('status') }}</span>
        </div>
    @endif
    @yield('content')
</div>
</body>
</html>
