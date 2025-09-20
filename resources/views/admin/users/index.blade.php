@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h4 class="mb-3">👥 Tài khoản / Phân quyền</h4>

    <!-- Tabs -->
    <ul class="nav nav-tabs" id="permissionTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="list-tab" data-bs-toggle="tab" href="#list" role="tab">📋 Danh sách tài khoản</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="create-tab" data-bs-toggle="tab" href="#create" role="tab">➕ Tạo tài khoản</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="banned-tab" data-bs-toggle="tab" href="#banned" role="tab">🚫 Tài khoản bị chặn</a>
        </li>
    </ul>

 

        <!-- Tạo tài khoản -->
        <div class="tab-pane fade" id="create" role="tabpanel">
            @include('admin.users.create_user')
        </div>

        <!-- Tài khoản bị chặn -->
        <div class="tab-pane fade" id="banned" role="tabpanel">
            @include('admin.users.banned_user')
        </div>
    </div>
</div>
@endsection
