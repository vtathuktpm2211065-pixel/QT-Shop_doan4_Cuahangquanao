@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h4>➕ Thêm Thành Viên Đội Ngũ</h4>

    {{-- Thông báo --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form thêm mới --}}
    <form action="{{ route('admin.team.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label>Họ tên</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            <small class="form-text text-muted">Phải kết thúc bằng <code>@gmail.com</code>.</small>
        </div>

        <div class="mb-3">
            <label>Mật khẩu</label>
            <div class="input-group">
                <input type="password" name="password" id="password" class="form-control" required>
                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password')">👁️</button>
            </div>
        </div>

        <div class="mb-3">
            <label>Xác nhận mật khẩu</label>
            <div class="input-group">
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password_confirmation')">👁️</button>
            </div>
        </div>

        {{-- Vai trò --}}
        <div class="mb-3">
            <label>Vai trò</label>
            <select name="role" id="role" class="form-control" required>
                <option value=""></option>
                @foreach($roles as $key => $label)
                    <option value="{{ $key }}" {{ old('role') == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        {{-- Quyền hạn --}}
        <div class="mb-3">
            <label>Quyền hạn</label>
            <select name="permissions[]" id="permissions" class="form-control" multiple required>
                @foreach($permissions as $key => $label)
                    <option value="{{ $key }}" {{ (collect(old('permissions'))->contains($key)) ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Ảnh đại diện</label>
            <input type="file" name="photo" class="form-control">
        </div>

        <div class="mb-3">
            <label>Mô tả</label>
            <textarea name="bio" class="form-control">{{ old('bio') }}</textarea>
        </div>

        <button class="btn btn-success">✅ Thêm thành viên</button>
    </form>
</div>

<script>
// Toggle password
function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === "password" ? "text" : "password";
}

// Đồng bộ quyền theo role từ controller
const permissionsByRole = @json($permissionsByRole);
const roleSelect = document.getElementById('role');
const permissionsSelect = document.getElementById('permissions');

function updatePermissions() {
    const selectedRole = roleSelect.value;
    const allowedPerms = permissionsByRole[selectedRole] || [];

    for (let option of permissionsSelect.options) {
        if (allowedPerms.includes(option.value)) {
            option.style.display = 'block';
            option.selected = true; // tick mặc định
        } else {
            option.style.display = 'none';
            option.selected = false;
        }
    }
}

roleSelect.addEventListener('change', updatePermissions);
window.addEventListener('load', updatePermissions);
</script>
@endsection