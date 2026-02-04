@extends('admin.layouts.app')

@section('content')
<div class="card">
    <h2>Sinh viên</h2>
    <table>
        <tr><th>Mã SV</th><th>Họ tên</th><th>Trạng thái</th></tr>
        @foreach($students as $student)
        <tr>
            <td>{{ $student->code }}</td>
            <td>{{ $student->full_name }}</td>
            <td><span class="badge">{{ $student->status_json['status'] ?? 'active' }}</span></td>
        </tr>
        @endforeach
    </table>
</div>
@endsection
