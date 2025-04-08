<!DOCTYPE html>
<html lang="en">

    <!-- Head -->
    <head>
        <meta charset="utf-8"/>
        <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport">
        <link href="{{ asset('img/apple-icon.png') }}" rel="apple-touch-icon" sizes="76x76">
        <link href="{{ asset('img/favicon.png') }}" rel="icon" type="image/png">
        <title>
            @yield('title', 'Default Title')
        </title>
        <!--     Fonts and icons     -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet"/>
        <!-- Nucleo Icons -->
        <link href="{{ asset('css/nucleo-icons.css') }}" rel="stylesheet"/>
        <link href="{{ asset('css/nucleo-svg.css') }}" rel="stylesheet"/>
        <!-- Font Awesome Icons -->
        <link crossorigin="anonymous" href="https://use.fontawesome.com/releases/v5.12.1/css/all.css" rel="stylesheet">
        <script crossorigin="anonymous" src="https://kit.fontawesome.com/42d5adcbca.js"></script>
        <link href="{{ asset('css/nucleo-svg.css') }}" rel="stylesheet"/>
        <!-- CSS Files -->
        <link href="{{ asset('css/soft-ui-dashboard.css?v=1.1.1') }}" id="pagestyle" rel="stylesheet"/>
        <style>
            #loader {
                opacity: 1;
                transition: opacity 0.5s ease;
            }

            #loader.hidden {
                opacity: 0;
                pointer-events: none;
            }
        </style>
    </head>

    <!-- Body -->
    <body class="g-sidenav-show  bg-gray-100">

        <!-- Loader -->
        <div id="loader"
             class="d-flex justify-content-center align-items-center position-fixed top-0 start-0 w-100 h-100"
             style="z-index: 9999; background-color: rgba(255, 255, 255, 0.9); display: none;">
            <div class="spinner-border text-primary" role="status" style="width: 4rem; height: 4rem;">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>

        @stack('styles')
        <!-- Sidebar -->
        @include('partials.aside')

        <!-- Main content -->
        <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
            <!-- Navbar -->
            @include('partials.navbar')

            <!-- Header -->
            @Yield('header')

            <!-- Page content -->
            <div class="container-fluid py-4">
                <!-- Content -->
                @yield('content')

                <!-- Footer -->
                @include('partials.footer')
            </div>
        </main>

        <!--   Core JS Files   -->
        <script src="{{ asset('js/core/popper.min.js') }}"></script>
        <script src="{{ asset('js/core/bootstrap.min.js') }}"></script>
        <script src="{{ asset('js/plugins/perfect-scrollbar.min.js') }}"></script>
        <script src="{{ asset('js/plugins/smooth-scrollbar.min.js') }}"></script>
        <script src="{{ asset('js/plugins/datatables.js') }}"></script>
        <script src="{{ asset('js/plugins/dropzone.min.js') }}"></script>
        <script src="{{ asset('js/plugins/chosen.min.js') }}"></script>

        <!-- Kanban scripts -->
        <script src="{{ asset('js/plugins/dragula/dragula.min.js') }}"></script>
        <script src="{{ asset('js/plugins/jkanban/jkanban.js') }}"></script>

        <!--   Soft UI Dashboard JS   -->
        <script>
            var win = navigator.platform.indexOf('Win') > -1;
            if (win && document.querySelector('#sidenav-scrollbar')) {
                var options = {
                    damping: '0.5'
                }
                Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
            }
        </script>

        <!-- Github buttons -->
        <script async defer src="https://buttons.github.io/buttons.js"></script>
        <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
        <script src="{{ asset('js/soft-ui-dashboard.min.js?v=1.1.1') }}"></script>

        <script>
            window.addEventListener('load', function () {
                const loader = document.getElementById('loader');
                if (loader) {
                    loader.classList.add('hidden');
                    setTimeout(() => loader.remove(), 600);
                }
            });
        </script>

        <!--   Page specific scripts   -->
        @stack('scripts')

    </body>
</html>
