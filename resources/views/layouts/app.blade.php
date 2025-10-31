<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Trang Admin')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Structured Data for Local SEO --}}
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "LocalBusiness",
      "name": "QT Shop",
      "image": "{{ url('/images/logo.png') }}",
      "@id": "",
      "url": "{{ url('/') }}",
      "telephone": "{{ $shopLocations->first()->phone ?? '' }}",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "{{ $shopLocations->first()->address ?? '' }}",
        "addressLocality": "Ho Chi Minh City",
        "addressRegion": "HCMC",
        "postalCode": "700000",
        "addressCountry": "VN"
      },
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": "{{ $shopLocations->first()->latitude ?? '' }}",
        "longitude": "{{ $shopLocations->first()->longitude ?? '' }}"
      },
      "openingHoursSpecification": {
        "@type": "OpeningHoursSpecification",
        "dayOfWeek": [
          "Monday",
          "Tuesday",
          "Wednesday",
          "Thursday",
          "Friday",
          "Saturday"
        ],
        "opens": "08:00",
        "closes": "21:00"
      }
    }
    </script>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
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
        <strong>© {{ date('Y') }} - Laravel AdminLTE thủ công.</strong>
    </footer>

</div>

<!-- JS -->
<script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>

@stack('scripts')

</body>
</html>
