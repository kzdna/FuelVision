<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'FuelVision')
    </title>

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >

    <link
        href="{{ secure_asset('css/fuelvision.css') }}"
        rel="stylesheet"
    >

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

    @else

        <main>
            @yield('content')
        </main>

    @endauth

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>

    @auth

        <script>
            (function () {
                var sidebar = document.getElementById('fvSidebar');
                var backdrop = document.getElementById('fvSidebarBackdrop');
                var toggle = document.getElementById('fvSidebarToggle');

                function closeSidebar() {
                    if (sidebar) {
                        sidebar.classList.remove('fv-sidebar-open');
                    }

                    if (backdrop) {
                        backdrop.classList.remove('show');
                    }
                }

                if (toggle && sidebar && backdrop) {
                    toggle.addEventListener('click', function () {
                        sidebar.classList.toggle('fv-sidebar-open');
                        backdrop.classList.toggle('show');
                    });

                    backdrop.addEventListener('click', closeSidebar);
                }
            })();
        </script>

    @endauth

    @stack('scripts')

</body>

</html>