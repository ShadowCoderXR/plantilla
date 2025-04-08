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
        <style></style>
    </head>

    <!-- Body -->
    <body class="g-sidenav-show  bg-gray-100">
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
            window.addEventListener('pageshow', function (event) {
                if (event.persisted || (performance && performance.getEntriesByType("navigation")[0].type === "back_forward")) {
                    location.reload();
                }
            });
        </script>
        <!--   Page specific scripts   -->
        @stack('scripts')

    </body>
</html>
