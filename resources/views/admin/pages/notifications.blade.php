@extends('admin.layouts.app')

@section('content')
<div class="card">
    <h2>Thông báo</h2>
    <table>
        <tr><th>Tiêu đề</th><th>Mức độ</th><th>Ngày</th></tr>
        @foreach($notifications as $notification)
        <tr><td>{{ $notification->title }}</td><td>{{ $notification->severity }}</td><td>{{ $notification->created_at }}</td></tr>
        @endforeach
    </table>
</div>
@endsection
