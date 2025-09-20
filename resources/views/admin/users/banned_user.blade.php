@extends('layouts.admin')

@section('content')
<div class="container-fluid mt-4">
    <h4 class="mb-4">🚫 Danh sách tài khoản bị chặn</h4>

    @if ($bannedUsers->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Không có tài khoản nào bị chặn.
        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-center mb-0">
                        <thead class="table-danger">
                            <tr>
                                <th>ID</th>
                                <th>Họ và Tên</th>
                                <th>Email</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bannedUsers as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td><strong>{{ $user->name }}</strong></td>
                                    <td>{{ $user->email }}</td>
                                    <td><span class="badge bg-danger">Đã bị chặn</span></td>
                                    <td>
                                        <form action="{{ route('admin.unbanUser', $user->id) }}" method="POST" class="d-inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-sm btn-success" onclick="return confirm('Bạn có chắc muốn mở chặn tài khoản này?')">
                                                ✅ Mở chặn
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <div class="mt-4">
        <a href="{{ route('admin.phanquyen') }}" class="btn btn-secondary">
            ⬅ Quay lại trang phân quyền
        </a>
    </div>
</div>
@endsection
