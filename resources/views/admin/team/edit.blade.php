@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h2>Chỉnh sửa thông tin</h2>

    <form action="{{ route('admin.team.update', $member->id) }}"
" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Họ tên</label>
            <input type="text" name="name" class="form-control" value="{{ $member->name }}" required>
        </div>



        <div class="mb-3">
            <label>Tiểu sử</label>
            <textarea name="bio" class="form-control">{{ $member->bio }}</textarea>
        </div>

        <div class="mb-3">
            <label>Ảnh mới (nếu muốn thay)</label>
            <input type="file" name="photo" class="form-control">
        </div>

        @if($member->photo)
            <img src="{{ asset('storage/' . $member->photo) }}" width="100" class="mb-3">
        @endif

        <button type="submit" class="btn btn-success">Cập nhật</button>
        <a href="{{ route('admin.team.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection