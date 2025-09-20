@extends('layouts.admin')

@section('content')
<div class="container py-4">
  <h4 class="mb-4">Hộp thư liên hệ</h4>
  <div class="card">
    <div class="card-body p-0">
      <ul class="list-group list-group-flush">
        @forelse($requests as $req)
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <div>
            <strong>{{ $req->name }}</strong> 
            <small class="text-muted">({{ $req->email }})</small>
            <p class="mb-0 text-truncate" style="max-width: 400px;">{{ $req->message }}</p>
          </div>
          <div class="text-end">
            <a href="{{ route('admin.support.chat', $req->id) }}" class="btn btn-sm btn-outline-primary">Xem</a>
            @if(!$req->is_read)
              <span class="badge bg-danger">Mới</span>
            @endif
          </div>
        </li>
        @empty
        <li class="list-group-item text-center">Không có yêu cầu nào.</li>
        @endforelse
      </ul>
    </div>
  </div>
</div>
@endsection
