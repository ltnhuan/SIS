@extends('admin.layouts.app')

@section('content')
<div class="card">
    <h2>Cảnh báo học vụ</h2>
    <form method="post" action="/quan-tri/canh-bao/tinh-gpa">
        @csrf
        <button>Tính GPA/CPA + Sinh cảnh báo học vụ</button>
    </form>
</div>
<div class="card">
    <h3>Danh sách cảnh báo</h3>
    <table>
        <tr><th>Sinh viên</th><th>Mức</th><th>Lý do</th></tr>
        @foreach($warnings as $warning)
        <tr><td>{{ $warning->student_id }}</td><td>{{ $warning->level }}</td><td>{{ $warning->reason }}</td></tr>
        @endforeach
    </table>
</div>
@endsection
