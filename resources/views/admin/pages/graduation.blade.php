@extends('admin.layouts.app')

@section('content')
<div class="card">
    <h2>Tốt nghiệp (demo)</h2>
    <form method="post" action="/quan-tri/tot-nghiep/duyet">
        @csrf
        <button>Duyệt ứng viên tốt nghiệp</button>
    </form>
</div>
<div class="card">
    <h3>Danh sách ứng viên</h3>
    <table>
        <tr><th>Sinh viên</th><th>Trạng thái</th></tr>
        @foreach($candidates as $candidate)
        <tr><td>{{ $candidate->student_id }}</td><td>{{ $candidate->status }}</td></tr>
        @endforeach
    </table>
</div>
@endsection
