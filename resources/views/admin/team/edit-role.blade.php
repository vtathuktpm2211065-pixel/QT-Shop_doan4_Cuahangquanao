@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h2>Phân quyền cho: {{ $member->name }}</h2>

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

    <form action="{{ route('admin.team.updateRole', $member->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Chọn vai trò --}}
        <div class="form-group mb-3">
            <label for="role-select">Vai trò</label>
            <select name="role" id="role-select" class="form-control" required>
                @foreach($roles as $key => $label)
                    <option value="{{ $key }}" {{ $member->role === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Chọn quyền --}}
        <div class="form-group mb-3">
            <label for="permissions">Quyền</label>
            <select name="permissions[]" id="permissions" class="form-control" multiple size="10">
                @foreach($permissions as $key => $label)
                    @php
                        $allowedRoles = [];
                        foreach ($permissionsByRole as $roleKey => $rolePermissions) {
                            if (in_array($key, $rolePermissions)) {
                                $allowedRoles[] = $roleKey;
                            }
                        }
                    @endphp
                    <option value="{{ $key }}"
                        data-roles="{{ implode(',', $allowedRoles) }}"
                        {{ in_array($key, $member->permissions ?? []) ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            <small class="form-text text-muted">
                Chỉ những quyền phù hợp với vai trò sẽ được hiển thị.
            </small>
        </div>

        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        <a href="{{ route('admin.team.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>

{{-- Script lọc quyền theo vai trò --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const roleSelect = document.getElementById('role-select');
    const permissionOptions = document.querySelectorAll('#permissions option');

    function filterPermissions(role) {
        permissionOptions.forEach(option => {
            const roles = option.getAttribute('data-roles').split(',');
            if (roles.includes(role)) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
                option.selected = false;
            }
        });
    }

    // Lọc khi load trang
    filterPermissions(roleSelect.value);

    // Lọc khi thay đổi vai trò
    roleSelect.addEventListener('change', function() {
        filterPermissions(this.value);
    });
});
</script>
@endsection