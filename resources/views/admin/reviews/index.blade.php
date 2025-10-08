@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4">📝 Quản lý đánh giá</h4>
      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

<form method="GET" class="row g-2 align-items-center bg-white p-3 rounded shadow-sm border">
    <div class="col-md-4">
        <div class="input-group">
            <input type="text" name="product" value="{{ request('product') }}"
                   class="form-control" placeholder="🔎 Tìm theo tên sản phẩm...">
        </div>
    </div>

    <div class="col-md-4">
        <div class="input-group">
          
            <input type="text" name="user" value="{{ request('user') }}"
                   class="form-control" placeholder="👤 Tìm theo tên người dùng...">
        </div>
    </div>

    <div class="col-md-4 d-flex justify-content-start align-items-center gap-3">
        <button type="submit" class="btn btn-primary px-4 d-flex align-items-center">
            <i class="fas fa-filter me-2"></i> Lọc
        </button>
        <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary px-4 d-flex align-items-center">
            <i class="fas fa-sync-alt me-2"></i> Làm mới
        </a>
    </div>
</form>


  
    <table class="table table-bordered align-middle">
          <thead class="table-primary text-center align-middle">
            <tr>
                <th>Sản phẩm</th>
                <th>Người dùng</th>
                <th>Sao</th>
                <th>Bình luận</th>
                <th>Phản hồi admin</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reviews as $review)
                <tr>
                    <td>{{ $review->product->name }}</td>
                    <td>{{ $review->user->name }}</td>
                    <td>{{ $review->rating }}⭐</td>
                    <td>{{ $review->comment }}</td>
                    <td>{{ $review->admin_reply ?? '-' }}</td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton{{ $review->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                ⋮
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $review->id }}">
                                @if(is_null($review->admin_reply))
                                    <a href="#" class="dropdown-item" data-toggle="modal" data-target="#replyModal{{ $review->id }}">
                                        📝 Phản hồi admin
                                    </a>
                                @else
                                    <span class="dropdown-item text-muted">📝 Đã phản hồi</span>
                                @endif
                               <form class="d-block" method="POST" action="{{ route('admin.reviews.destroy', $review->id) }}" onsubmit="return confirm('Bạn có chắc muốn xóa đánh giá này không?');">
    @csrf
    @method('DELETE')
    <button type="submit" class="dropdown-item text-danger">
        🗑️ Xóa
    </button>
</form>

                            </div>
                        </div>

                        @if(is_null($review->admin_reply))
                            <!-- Modal Phản hồi admin -->
                            <div class="modal fade" id="replyModal{{ $review->id }}" tabindex="-1" role="dialog" aria-labelledby="replyModalLabel{{ $review->id }}" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('admin.reviews.reply', $review->id) }}">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="replyModalLabel{{ $review->id }}">Phản hồi đánh giá</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="admin_reply_{{ $review->id }}">Phản hồi của Admin</label>
                                                    <textarea class="form-control" id="admin_reply_{{ $review->id }}" name="admin_reply" rows="4" required maxlength="2000">{{ old('admin_reply') }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                                                <button type="submit" class="btn btn-primary">Gửi phản hồi</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Phân trang -->
    <div>
        {{ $reviews->links('pagination::bootstrap-4') }}
    </div>
</div>
@endsection
<style>
    .input-group-text {
    border-right: none;
}
.form-control {
    border-left: none;
}
.btn {
    border-radius: 8px;
}

</style>