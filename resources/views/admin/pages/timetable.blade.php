@extends('admin.layouts.app')

@section('content')
<div class="card">
    <h2>Xếp thời khóa biểu</h2>
    <div class="actions">
        <form method="post" action="/quan-tri/xep-tkb/tao-run">
            @csrf
            <button>Tạo run xếp TKB</button>
        </form>
        <form method="post" action="/quan-tri/xep-tkb/chay-solver">
            @csrf
            <button class="secondary">Chạy solver (demo)</button>
        </form>
        <form method="post" action="/quan-tri/xep-tkb/cong-bo">
            @csrf
            <button>Công bố TKB</button>
        </form>
    </div>
</div>
<div class="card">
    <h3>Run gần đây</h3>
    <table>
        <tr><th>ID</th><th>Trạng thái</th><th>Chế độ</th></tr>
        @foreach($runs as $run)
        <tr><td>{{ $run->id }}</td><td>{{ $run->status }}</td><td>{{ $run->mode }}</td></tr>
        @endforeach
    </table>
</div>
<div class="card">
    <h3>Phiên bản công bố</h3>
    <table>
        <tr><th>ID</th><th>Học kỳ</th><th>Ngày công bố</th></tr>
        @foreach($publications as $publication)
        <tr><td>{{ $publication->id }}</td><td>{{ $publication->term_id }}</td><td>{{ $publication->published_at }}</td></tr>
        @endforeach
    </table>
</div>
@endsection
