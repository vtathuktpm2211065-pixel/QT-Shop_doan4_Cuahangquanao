@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Quản lý tài khoản / phân quyền</h4>
        
        <div class="d-flex justify-content-end mb-3" style="gap: 10px;">
    <a href="{{ route('admin.createUser') }}" class="btn btn-success">
         Tạo tài khoản
    </a>
    <a href="{{ route('admin.bannedList') }}" class="btn btn-outline-danger">
         Danh sách bị chặn
    </a>
</div>

    </div>
<form method="GET" action="{{ route('admin.users') }}" class="d-flex mb-3 align-items-center" style="gap: 10px; max-width: 1000px; width: 100%;">
    <input type="text" name="search" class="form-control" placeholder="Tìm theo tên hoặc email..." value="{{ request('search') }}" style="max-width: 700px; flex-grow: 1;">
    
    <button type="submit" class="btn btn-primary" style="min-width: 120px; padding: 6px 10px; font-size: 14px;">
         Tìm kiếm
    </button>

    <a href="{{ route('admin.users') }}" class="btn btn-secondary" style="min-width: 120px; padding: 6px 10px; font-size: 14px;">
        Quay lại
    </a>
</form>





    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
    </div>
@endif


    <div class="table-responsive mt-3">
        <table class="table table-bordered text-center table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>User ID</th>
                    <th>Tài khoản</th>
                    <th>Họ và Tên</th>
                    <th>Email</th>
                    <th>Quyền hạn</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->username ?? 'N/A' }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @php $roles = $user->getRoleNames(); @endphp
                        @if ($roles->contains('admin'))
                            <span class="badge bg-danger">Toàn quyền module</span>
                        @elseif ($roles->isEmpty())
                            <span class="text-muted">Không có</span>
                        @else
                            @foreach ($roles as $role)
                                <span class="badge bg-info text-dark">{{ $role }}</span>
                            @endforeach
                        @endif
                    </td>
                    <td>
                        @if($user->banned ?? false)
                            <span class="badge bg-danger">Đã bị chặn</span>
                        @else
                            <span class="badge bg-success">Hoạt động</span>
                        @endif
                    </td>
                   <td>

                    <a href="{{ route('admin.editUserInfo', $user->id) }}" class="btn btn-sm btn-outline-secondary mb-1">✏️ Sửa thông tin</a>
    @if(!$user->hasRole('admin'))
        {{-- Sửa quyền hạn --}}
        <a href="{{ route('admin.editUserRole', $user->id) }}" class="btn btn-sm btn-outline-primary mb-1">✏️ Sửa quyền</a>

        {{-- Chặn hoặc Mở chặn --}}
        <form action="{{ route('admin.chanMoNguoiDung', $user->id) }}" method="POST" class="d-inline-block">
            @csrf
            @method('PATCH')
            @if ($user->banned)
                <button class="btn btn-sm btn-outline-success mb-1" onclick="return confirm('Mở chặn tài khoản này?')">✅ Mở chặn</button>
            @else
                <button class="btn btn-sm btn-outline-warning mb-1" onclick="return confirm('Chặn tài khoản này?')">🚫 Chặn</button>
            @endif
        </form>

        {{-- Xoá người dùng --}}
        <form action="{{ route('admin.deleteUser', $user->id) }}" method="POST" class="d-inline-block">
            @csrf
            @method('DELETE')
            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Xoá tài khoản này?')">🗑 Xoá</button>
        </form>
    @else
      
    @endif
</td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Phân trang --}}
    <div class="d-flex justify-content-center mt-3">
        {{ $users->links() }}
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.select-all').forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                const targetId = this.getAttribute('data-target');
                const select = document.getElementById(targetId);
                if (select) {
                    for (let option of select.options) {
                        option.selected = this.checked;
                    }
                }
            });
        });
    });
</script>
@endsection
