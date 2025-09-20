@extends('app')

@section('content')
<div class="container py-5" style="min-height: 80vh;">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="card shadow rounded-4">
        <div class="card-header bg-primary text-white text-center rounded-top-4">
          <h4 class="mb-0">{{ __('Reset Password') }}</h4>
        </div>
        <div class="card-body p-4">
          @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              {{ session('status') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          @endif

          <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-3">
              <label for="email" class="form-label fw-semibold">{{ __('Email Address') }}</label>
              <input id="email" 
                     type="email" 
                     class="form-control @error('email') is-invalid @enderror" 
                     name="email" 
                     value="{{ old('email') }}" 
                     required 
                     autocomplete="email" 
                     autofocus 
                     placeholder="Nhập email của bạn...">

              @error('email')
                <div class="invalid-feedback">
                  {{ $message }}
                </div>
              @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100 fw-semibold">
              {{ __('Gửi liên kết đặt lại mật khẩu') }}
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
