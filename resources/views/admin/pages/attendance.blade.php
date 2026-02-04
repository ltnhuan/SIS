@extends('admin.layouts.app')

@section('content')
<div class="card">
    <h2>Điểm danh</h2>
    <form method="post" action="/quan-tri/diem-danh/tao-buoi">
        @csrf
        <button>Tạo buổi điểm danh 7 ngày tới</button>
    </form>
</div>
<div class="card">
    <h3>Buổi điểm danh gần đây</h3>
    <table>
        <tr><th>Ngày</th><th>QR</th><th>Hạn</th></tr>
        @foreach($sessions as $session)
        <tr><td>{{ $session->session_date }}</td><td>{{ $session->qr_token }}</td><td>{{ $session->expires_at }}</td></tr>
        @endforeach
    </table>
</div>
@endsection
