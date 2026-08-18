<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'FuelVision')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >

    <link href="{{ asset('css/fuelvision.css') }}" rel="stylesheet">

    @stack('styles')
</head>

<body>

@auth
    <div class="fv-app">

        @include('partials.sidebar')

        <div class="fv-main">

            @include('partials.navbar')

            <main class="fv-content">
                @yield('content')
            </main>

        </div>

    </div>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>

    <script>
        (function () {
            var sidebar = document.getElementById('fvSidebar');
            var backdrop = document.getElementById('fvSidebarBackdrop');
            var toggle = document.getElementById('fvSidebarToggle');

            function closeSidebar() {
                sidebar.classList.remove('fv-sidebar-open');
                backdrop.classList.remove('show');
            }

            if (toggle) {
                toggle.addEventListener('click', function () {
                    sidebar.classList.toggle('fv-sidebar-open');
                    backdrop.classList.toggle('show');
                });
            }

            if (backdrop) {
                backdrop.addEventListener('click', closeSidebar);
            }
        })();
    </script>
@else
    {{-- Guest pages (login) keep a plain centered container, no sidebar/navbar --}}
    <div class="container py-5">
        @yield('content')
    </div>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>
@endauth

@stack('scripts')

</body>
</html>
