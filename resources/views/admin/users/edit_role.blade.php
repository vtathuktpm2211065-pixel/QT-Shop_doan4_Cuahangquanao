@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h4>⚙️ Cập nhật quyền cho: {{ $user->name }}</h4>

    <form action="{{ route('admin.updateUserRole', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Vai trò --}}
        <div class="form-group mb-3">
            <label for="role-select">Chọn vai trò:</label>
            <select name="role" id="role-select" class="form-control">
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                        {{ ucfirst($role->name) }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Checkbox chọn tất cả quyền --}}
        <div class="form-check mb-2">
            <input type="checkbox" class="form-check-input" id="select-all-permissions">
            <label class="form-check-label" for="select-all-permissions">Chọn tất cả quyền hiển thị</label>
        </div>

        {{-- Quyền cụ thể --}}
        <div class="form-group mb-3">
            <label for="permissions">Chức năng được cấp (quyền):</label>
            <select name="permissions[]" id="permissions" class="form-control" multiple size="10">
                @foreach($permissions as $permission)
                    <option value="{{ $permission->name }}"
                        {{ $user->permissions->contains('name', $permission->name) ? 'selected' : '' }}>
                        {{ $permission->description ?? $permission->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">💾 Cập nhật</button>
        <a href="{{ route('admin.phanquyen') }}" class="btn btn-secondary">⬅ Quay lại</a>
    </form>
</div>
@endsection

@section('scripts')
<script>
    const permissionsByRole = @json($permissionsByRole);
    const permissionSelect = document.getElementById('permissions');
    const roleSelect = document.getElementById('role-select');
    const selectAllCheckbox = document.getElementById('select-all-permissions');

    function updatePermissionsDisplay(role) {
        const allowed = permissionsByRole[role] ?? [];
        [...permissionSelect.options].forEach(opt => {
            const shouldShow = allowed.includes(opt.value);
            opt.style.display = shouldShow ? 'block' : 'none';
            if (!shouldShow) {
                opt.selected = false; // Bỏ chọn nếu không được phép
            }
        });

        // Reset checkbox chọn tất cả khi đổi vai trò
        selectAllCheckbox.checked = false;
    }

    document.addEventListener('DOMContentLoaded', () => {
        updatePermissionsDisplay(roleSelect.value);

        roleSelect.addEventListener('change', function () {
            updatePermissionsDisplay(this.value);
        });

        // Xử lý chọn tất cả quyền hiển thị
        selectAllCheckbox.addEventListener('change', function () {
            const isChecked = this.checked;
            [...permissionSelect.options].forEach(opt => {
                if (opt.style.display !== 'none') {
                    opt.selected = isChecked;
                }
            });
        });
    });
</script>
@endsection
