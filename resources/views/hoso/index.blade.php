@extends('app') {{-- Sử dụng layout chính của bạn --}}
@section('title', 'Thông tin tài khoản')

@section('content')
<div class="container py-4 d-flex justify-content-center">
    <div class="card shadow rounded" style="width: 500px;">
        <div class="card-body text-center">
            <div class="mb-3">
                <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('default-avatar.png') }}"
                     alt="Avatar" class="rounded-circle" width="120" height="120"
                     style="object-fit: cover;">
            </div>
            <h4 class="mb-1">{{ $user->name }}</h4>
            <p class="mb-1 text-muted">{{ $user->email }}</p>
            <p class="mb-3 text-muted">{{ $user->phone ?? 'Chưa cập nhật số điện thoại' }}</p>

            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal">
                Cập nhật
            </button>

        <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
            Thay đổi mật khẩu
        </button>
        </div>
    </div>
</div>

<!-- Modal cập nhật -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" action="{{ route('hoso.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Cập nhật thông tin</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
        </div>

        <div class="modal-body">
           <div class="mb-3">
    <label class="form-label">Tên đăng nhập</label>
    <input type="text" name="username" class="form-control"
           value="{{ old('username', $user->username) }}" required>
</div>


            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control"
                       value="{{ old('email', $user->email) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Số điện thoại</label>
                <input type="text"
       name="phone"
       class="form-control"
       value="{{ old('phone', $user->phone) }}"
       placeholder="VD: 0901234567"
       pattern="^0[0-9]{9}$"
       maxlength="10"
       oninput="this.value = this.value.replace(/[^0-9]/g, '')"
       required>

            </div>

            <div class="mb-3">
                <label class="form-label">Ảnh đại diện</label>
                <input type="file" name="avatar" class="form-control" accept="image/*" onchange="previewAvatar(this)">
               <div class="mt-3 text-center">
    <img id="avatarPreview"
         src="{{ $user->avatar ? Storage::url($user->avatar) : asset('default-avatar.png') }}"
         alt="Xem trước ảnh đại diện" 
         class="rounded-circle shadow"
         width="120" 
         height="120"
         style="object-fit: cover; border: 2px solid #95ff00;">
    <div class="small text-muted mt-1" id="avatarFileName"></div>
</div>
            </div>
        </div>

        <div class="modal-header">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
          <button type="submit" class="btn btn-success">Lưu thay đổi</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal thay đổi mật khẩu -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" action="{{ route('hoso.changePassword') }}">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title" id="changePasswordModalLabel">Thay đổi mật khẩu</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
        </div>
        <div class="modal-body">

          <div class="mb-3">
            <label class="form-label">Mật khẩu hiện tại</label>
            <input type="password" name="current_password" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Mật khẩu mới</label>
            <input type="password" name="new_password" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Nhập lại mật khẩu mới</label>
            <input type="password" name="new_password_confirmation" class="form-control" required>
          </div>

        </div>
        <div class="modal-header">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
          <button type="submit" class="btn btn-success">Đổi mật khẩu</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection


<script>
function previewAvatar(input) {
    const preview = document.getElementById('avatarPreview');
    const fileNameDisplay = document.getElementById('avatarFileName');
    const file = input.files[0];

    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            fileNameDisplay.textContent = "Ảnh đã chọn: " + file.name;
        }
        reader.readAsDataURL(file);
    } else {
        fileNameDisplay.textContent = "";
    }
}
</script>
