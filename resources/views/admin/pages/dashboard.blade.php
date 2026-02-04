@extends('admin.layouts.app')

@section('content')
<div class="card">
    <h2>Tổng quan</h2>
    <p>Dữ liệu tổng hợp SIS demo.</p>
</div>
<div class="card">
    <table>
        <tr><th>Chỉ số</th><th>Giá trị</th></tr>
        <tr><td>Số sinh viên</td><td>{{ $studentCount }}</td></tr>
        <tr><td>Số lớp học phần</td><td>{{ $classCount }}</td></tr>
        <tr><td>% có thời khóa biểu</td><td>{{ $classCount > 0 ? round(($assignedCount / $classCount) * 100, 1) : 0 }}%</td></tr>
        <tr><td>% đủ điều kiện dự thi</td><td>{{ $studentCount > 0 ? round(($eligibleCount / $studentCount) * 100, 1) : 0 }}%</td></tr>
        <tr><td>Số cảnh báo học vụ</td><td>{{ $warningCount }}</td></tr>
        <tr><td>Thông báo Khẩn/Cao</td><td>{{ $urgentNotifications }} / {{ $highNotifications }}</td></tr>
    </table>
</div>
@endsection
