@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h4>➕ Tạo tài khoản người dùng mới</h4>

    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf

        <!-- Username -->
        <div class="form-group mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>
            <small class="form-text text-muted">Chỉ chứa chữ cái, số và dấu gạch dưới (_), không dấu cách.</small>
            @error('username') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <!-- Họ tên -->
<div class="form-group mb-3">
    <label>Họ tên</label>
    <input type="text" name="name"
           class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name') }}" required>
    <small class="form-text text-muted">
        Chỉ chứa chữ cái và khoảng trắng, không chứa số hay ký tự đặc biệt.
    </small>
    @error('name')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>


<!-- Email -->
<div class="form-group mb-3">
    <label>Email</label>
    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
    <small class="form-text text-muted">Phải là địa chỉ email hợp lệ và kết thúc bằng <code>@gmail.com</code>.</small>
    @error('email')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>


        <!-- Mật khẩu -->
        <div class="form-group mb-3">
            <label for="password">Mật khẩu</label>
            <div class="input-group">
                <input type="password" name="password" class="form-control" id="password" required autocomplete="new-password">
                <span class="input-group-text bg-white" style="cursor:pointer;" onclick="togglePassword('password', 'eye-icon')">
                    <span id="eye-icon">🙈</span>
                </span>
            </div>
            <small class="form-text text-muted">
                Mật khẩu ít nhất 8 ký tự, gồm chữ hoa, chữ thường, số và ký tự đặc biệt.
            </small>
            @error('password') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <!-- Xác nhận mật khẩu -->
        <div class="form-group mb-3">
            <label for="password_confirmation">Xác nhận mật khẩu</label>
            <div class="input-group">
                <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" required autocomplete="new-password">
                <span class="input-group-text bg-white" style="cursor:pointer;" onclick="togglePassword('password_confirmation', 'eye-icon-confirm')">
                    <span id="eye-icon-confirm">🙈</span>
                </span>
            </div>
        </div>

        <!-- Vai trò -->
        <div class="form-group mb-3">
            <label>Chọn vai trò:</label>
            <select name="role" id="role-select" class="form-control" required>
                @foreach ($roles as $role)
                    <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                        {{ ucfirst($role->name) }}
                    </option>
                @endforeach
            </select>
            @error('role') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <!-- Phân quyền -->
        <div class="form-group mb-3">
            <label>Chức năng được cấp:</label>
            <select name="permissions[]" id="permissions" class="form-control" multiple>
                @foreach ($permissions as $permission)
                    <option value="{{ $permission->name }}"
                        {{ (collect(old('permissions'))->contains($permission->name)) ? 'selected' : '' }}>
                        {{ $permission->description ?? $permission->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button class="btn btn-success">✅ Tạo tài khoản</button>
        <a href="{{ route('admin.phanquyen') }}" class="btn btn-secondary">⬅ Quay lại</a>
    </form>
</div>
@endsection

@section('scripts')
<script>
    @isset($permissionsByRole)
        const permissionsByRole = @json($permissionsByRole);
    @else
        const permissionsByRole = {};
    @endisset

    const permissionSelect = document.getElementById('permissions');
    const roleSelect = document.getElementById('role-select');

    function updatePermissionsForRole(role) {
        const allowed = permissionsByRole[role] ?? [];
        [...permissionSelect.options].forEach(opt => {
            opt.style.display = allowed.includes(opt.value) ? 'block' : 'none';
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        updatePermissionsForRole(roleSelect.value);
        roleSelect.addEventListener('change', function () {
            updatePermissionsForRole(this.value);
        });
    });

    // Toggle password visibility
    function togglePassword(inputId, iconId) {
        const field = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        const isVisible = field.type === 'text';
        field.type = isVisible ? 'password' : 'text';
        icon.textContent = isVisible ? '🙈' : '👁️';
    }
</script>

@endsection
