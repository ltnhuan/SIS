@extends('admin.layouts.app')

@section('content')
<div class="card">
    <h2>Điểm số & GPA</h2>
    <div class="actions">
        <form method="post" action="/quan-tri/diem-so/cham-ngau-nhien">
            @csrf
            <button>Chấm điểm ngẫu nhiên + Khóa sổ</button>
        </form>
        <form method="post" action="/quan-tri/diem-so/khoa-so">
            @csrf
            <button class="secondary">Khóa sổ điểm</button>
        </form>
    </div>
</div>
<div class="card">
    <h3>Sổ điểm</h3>
    <table>
        <tr><th>ID</th><th>Trạng thái</th></tr>
        @foreach($gradeBooks as $book)
        <tr><td>{{ $book->id }}</td><td>{{ $book->status }}</td></tr>
        @endforeach
    </table>
</div>
@endsection
