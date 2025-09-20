@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 text-primary">📬 Danh sách yêu cầu hỗ trợ</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($requests->isEmpty())
        <div class="alert alert-info">Không có yêu cầu hỗ trợ nào.</div>
    @else
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover table-striped table-bordered mb-0 align-middle text-center">
                    <thead class="thead-light">
                        <tr>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th>Điện thoại</th>
                            <th>Nội dung</th>
                            <th>Ngày gửi</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $req)
                            <tr>
                                <td>{{ $req->name }}</td>
                                <td>{{ $req->email }}</td>
                                <td>{{ $req->phone ?? '-' }}</td>
                                <td class="text-left">{{ \Illuminate\Support\Str::limit($req->message, 50) }}</td>
                                <td>{{ $req->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton{{ $req->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="">
                                            <i class=""></i>
                                        </button>
                                       
                                        <div class="dropdown-menu dropdown-menu-right p-1" aria-labelledby="dropdownMenuButton{{ $req->id }}" style="min-width: 60px;">
                                            <a class="dropdown-item d-flex align-items-center justify-content-center position-relative" href="{{ route('admin.support.chat', $req->id) }}" title="Xem / Trả lời">
                                                <i class="fas fa-comments fa-lg text-primary"></i>
                                                @if($req->replies->where('is_admin', false)->where('is_read', false)->count() > 0)
                                                    <span style="position: absolute; top: 6px; right: 8px; font-size: 14px; color: #dc3545;">&#x1F534;</span>
                                                @endif
                                            </a>
                                            <form action="{{ route('admin.support.delete', $req->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa yêu cầu này?');" class="m-0">
                                                @csrf
                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger" title="Xóa đánh giá" style="padding: 0.375rem 1rem;">
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
        </div>
    @endif
</div>
@endsection