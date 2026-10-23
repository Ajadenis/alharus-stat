<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MDT Al-Harus</title>

    <!-- favicon -->
    <link rel="shortcut icon" href="{{ asset('images/logo-title.ico') }}" />

    <!-- fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,300;0,400;0,700;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Boostrap icons -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    />

    <!-- Feather Icon -->
    <script src="https://unpkg.com/feather-icons"></script>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">

    <!-- CSS khusus per halaman -->
    @stack('styles')
    
  </head>
<body>
    @include('partials.navbar')

    <main>
        @yield('content')
    </main>
    
    @include('partials.footer')


    <!-- JS -->
    <script src="{{ asset('js/toggle.js') }}"></script>
    @stack('slideshow')

    <!-- Feather Icon -->
    <script>
      feather.replace();
    </script>

    <!-- Vercel -->
    <script>
      window.va =
        window.va ||
        function () {
          (window.vaq = window.vaq || []).push(arguments);
        };
    </script>
    <script defer src="/_vercel/insights/script.js"></script>

</body>
</html>