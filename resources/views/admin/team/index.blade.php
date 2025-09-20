@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Danh sách thành viên</h2>

    <a href="{{ route('admin.team.create') }}" class="btn btn-primary mb-3">+ Thêm thành viên</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <hr class="my-4">

    {{-- Bảng danh sách nhân viên --}}
    <h3>Danh Sách Nhân Viên</h3>

    @if(isset($teamMembers) && $teamMembers->count())
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="thead-dark">
                    <tr>
                        <th>Ảnh</th>
                        <th>Họ tên</th>
                        <th>Vai trò</th>
                        <th>Quyền</th>
                        <th>Trạng thái</th>
                        <th>Mô tả</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($teamMembers as $member)
                        <tr>
                            {{-- Ảnh đại diện --}}
                            <td>
                                @if ($member->photo)
                                    <img src="{{ asset('storage/' . $member->photo) }}" 
                                         alt="Ảnh đại diện" 
                                         class="rounded" 
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <em>Không có</em>
                                @endif
                            </td>

                            {{-- Họ tên --}}
                            <td>{{ $member->name }}</td>

                            {{-- Vai trò --}}
                            <td>
                                {{ $member->role && isset($roles[$member->role]) ? $roles[$member->role] : 'Chưa phân quyền' }}
                            </td>

                            {{-- Quyền --}}
                            <td>
                                @php
                                    $memberPermissions = is_string($member->permissions) ? json_decode($member->permissions, true) : $member->permissions ?? [];
                                    $displayPermissions = array_map(fn($perm) => $permissions[$perm] ?? $perm, $memberPermissions);
                                @endphp
                                {{ $displayPermissions && count($displayPermissions) > 0 ? implode(', ', $displayPermissions) : 'Chưa phân quyền' }}
                            </td>

                            {{-- Trạng thái --}}
                            <td>
                                @if ($member->banned)
                                    <span class="badge badge-danger">Bị chặn</span>
                                @else
                                    <span class="badge badge-success">Hoạt động</span>
                                @endif
                            </td>

                            {{-- Mô tả --}}
                            <td>{{ $member->bio ?? '-' }}</td>

                            {{-- Hành động --}}
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-secondary btn-sm dropdown-toggle" 
                                            type="button" 
                                            id="dropdownMenuButton{{ $member->id }}" 
                                            data-toggle="dropdown" 
                                            aria-haspopup="true" 
                                            aria-expanded="false">
                                        ⚙️
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton{{ $member->id }}">
                                        <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.team.editRole', $member->id) }}">
                                            ✏️ Sửa quyền
                                        </a>
                                        <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.team.edit', $member->id) }}">
                                            📝 Sửa thông tin
                                        </a>
                                        <form action="{{ route('admin.team.toggleBan', $member->id) }}" method="POST" onsubmit="return confirm('{{ $member->banned ? 'Mở chặn thành viên này?' : 'Chặn thành viên này?' }}')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="dropdown-item text-warning d-flex align-items-center">
                                                {{ $member->banned ? '✅ Mở chặn' : '🚫 Chặn' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.team.destroy', $member->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa không?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger d-flex align-items-center">
                                                🗑️ Xóa
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p>Chưa có nhân viên nào.</p>
    @endif
</div>
@endsection
