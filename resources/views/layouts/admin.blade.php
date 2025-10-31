<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>@yield('title', 'Trang Admin')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSS -->
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">

  <!-- Firebase App -->
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
<!-- Firebase Messaging -->
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js"></script>

<script>
  // Firebase configuration
  const firebaseConfig = {
    apiKey: "{{ env('FIREBASE_API_KEY') }}",
    authDomain: "{{ env('FIREBASE_AUTH_DOMAIN') }}",
    projectId: "{{ env('FIREBASE_PROJECT_ID') }}",
    storageBucket: "{{ env('FIREBASE_STORAGE_BUCKET') }}",
    messagingSenderId: "{{ env('FIREBASE_MESSAGING_SENDER_ID') }}",
    appId: "{{ env('FIREBASE_APP_ID') }}"
  };

  // Initialize Firebase
  firebase.initializeApp(firebaseConfig);
</script>

</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <!-- Navbar -->
  @include('admin.partials.navbar')

  <!-- Sidebar -->
  @include('admin.partials.sidebar')

  <!-- Nội dung chính -->
  <div class="content-wrapper p-3">
      @yield('content')
  </div>

  <!-- Footer -->
  <footer class="main-footer text-center">
      <strong>&copy; {{ date('Y') }} - Laravel AdminLTE thủ công.</strong>
  </footer>

</div>

<!-- JS -->
<script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>
@yield('scripts')

</body>
</html>
