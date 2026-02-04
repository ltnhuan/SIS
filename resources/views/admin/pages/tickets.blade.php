@extends('admin.layouts.app')

@section('content')
<div class="card">
    <h2>Ticket/Đơn từ</h2>
    <table>
        <tr><th>ID</th><th>Loại</th><th>Trạng thái</th></tr>
        @foreach($tickets as $ticket)
        <tr><td>{{ $ticket->id }}</td><td>{{ $ticket->type }}</td><td>{{ $ticket->status }}</td></tr>
        @endforeach
    </table>
</div>
@endsection
