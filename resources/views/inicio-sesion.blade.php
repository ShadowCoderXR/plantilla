<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('img/apple-icon.png') }}">
  <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
  <title>
    Soft UI Dashboard PRO by Creative Tim
  </title>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="{{ asset('css/nucleo-icons.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/nucleo-svg.css') }}" rel="stylesheet" />
  <!-- Font Awesome Icons -->
    <link crossorigin="anonymous" href="https://use.fontawesome.com/releases/v5.12.1/css/all.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link href="{{ asset('css/nucleo-svg.css') }}" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="{{ asset('css/soft-ui-dashboard.css?v=1.1.1') }}" rel="stylesheet" />
</head>

<body class="">
  <main class="main-content main-content-bg mt-0">
    <section>
      <div class="page-header min-vh-75">
        <div class="container">
          <div class="row">
            <div class="col-xl-4 col-lg-5 col-md-6 d-flex flex-column mx-auto">
              <div class="card card-plain mt-8">
                <div class="card-header pb-0 text-start">
                  <h3 class="font-weight-bolder text-info text-gradient">Bienvenido</h3>
                  <p class="mb-0">Ingrese sus credenciales para iniciar sesión</p>
                </div>
                <div class="card-body">
                    <form role="form" class="text-start" method="POST" action="{{ route('iniciar.sesion') }}">
                        @csrf

                        <label>Correo Electrónico</label>
                        <div class="mb-3">
                            <input type="email"
                                   name="correo"
                                   class="form-control @error('correo') is-invalid @enderror"
                                   placeholder="Correo Electrónico"
                                   value="{{ old('correo') }}"
                                   required>
                            @error('correo')
                            <div id="correo-error" class="text-danger text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <label>Contraseña</label>
                        <div class="mb-3 position-relative">
                            <input type="password"
                                   name="contrasena"
                                   id="contrasena"
                                   class="form-control @error('contrasena') is-invalid @enderror"
                                   placeholder="Contraseña"
                                   required>
                            <span id="ojo" class="position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer"
                                  onclick="togglePasswordVisibility()" style="z-index: 10;">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </span>
                            @error('contrasena')
                            <div id="contrasena-error" class="text-danger text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn bg-gradient-info w-100 mt-4 mb-0">Iniciar Sesión</button>
                        </div>
                    </form>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="oblique position-absolute top-0 h-100 d-md-block d-none me-n8">
                <div class="oblique-image bg-cover position-absolute fixed-top ms-auto h-100 z-index-0 ms-n6" style="background-image:url('{{ asset('img/curved-images/curved9.jpg') }}')"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
  <!-- -------- START FOOTER 3 w/ COMPANY DESCRIPTION WITH LINKS & SOCIAL ICONS & COPYRIGHT ------- -->
  <footer class="footer py-5">
    <div class="container">
      <div class="row">
        <div class="col-lg-8 mb-4 mx-auto text-center">
          <a href="javascript:;" target="_blank" class="text-secondary me-xl-5 me-3 mb-sm-0 mb-2">
            Company
          </a>
          <a href="javascript:;" target="_blank" class="text-secondary me-xl-5 me-3 mb-sm-0 mb-2">
            About Us
          </a>
          <a href="javascript:;" target="_blank" class="text-secondary me-xl-5 me-3 mb-sm-0 mb-2">
            Team
          </a>
          <a href="javascript:;" target="_blank" class="text-secondary me-xl-5 me-3 mb-sm-0 mb-2">
            Products
          </a>
          <a href="javascript:;" target="_blank" class="text-secondary me-xl-5 me-3 mb-sm-0 mb-2">
            Blog
          </a>
          <a href="javascript:;" target="_blank" class="text-secondary me-xl-5 me-3 mb-sm-0 mb-2">
            Pricing
          </a>
        </div>
        <div class="col-lg-8 mx-auto text-center mb-4 mt-2">
          <a href="javascript:;" target="_blank" class="text-secondary me-xl-4 me-4">
            <span class="text-lg fab fa-dribbble"></span>
          </a>
          <a href="javascript:;" target="_blank" class="text-secondary me-xl-4 me-4">
            <span class="text-lg fab fa-twitter"></span>
          </a>
          <a href="javascript:;" target="_blank" class="text-secondary me-xl-4 me-4">
            <span class="text-lg fab fa-instagram"></span>
          </a>
          <a href="javascript:;" target="_blank" class="text-secondary me-xl-4 me-4">
            <span class="text-lg fab fa-pinterest"></span>
          </a>
          <a href="javascript:;" target="_blank" class="text-secondary me-xl-4 me-4">
            <span class="text-lg fab fa-github"></span>
          </a>
        </div>
      </div>
      <div class="row">
        <div class="col-8 mx-auto text-center mt-1">
          <p class="mb-0 text-secondary">
            Copyright © <script>
              document.write(new Date().getFullYear())
            </script> Soft by Creative Tim.
          </p>
        </div>
      </div>
    </div>
  </footer>
  <!-- -------- END FOOTER 3 w/ COMPANY DESCRIPTION WITH LINKS & SOCIAL ICONS & COPYRIGHT ------- -->
  <!--   Core JS Files   -->
  <script src="{{ asset('js/core/popper.min.js') }}"></script>
  <script src="{{ asset('js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('js/plugins/perfect-scrollbar.min.js') }}"></script>
  <script src="{{ asset('js/plugins/smooth-scrollbar.min.js') }}"></script>
  <!-- Kanban scripts -->
  <script src="{{ asset('js/plugins/dragula/dragula.min.js') }}"></script>
  <script src="{{ asset('js/plugins/jkanban/jkanban.js') }}"></script>
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
</body>

<script>
    function togglePasswordVisibility() {
        const input = document.getElementById('contrasena');
        const icon = document.getElementById('toggleIcon');
        const isVisible = input.type === 'text';

        input.type = isVisible ? 'password' : 'text';
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    }
</script>

<script>
    function togglePasswordVisibility() {
        const input = document.getElementById('contrasena');
        const icon = document.getElementById('toggleIcon');
        const isVisible = input.type === 'text';

        input.type = isVisible ? 'password' : 'text';
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    }

    document.addEventListener('DOMContentLoaded', () => {
        const correoInput = document.querySelector('[name="correo"]');
        const contrasenaInput = document.getElementById('contrasena');

        correoInput.addEventListener('input', () => {
            correoInput.classList.remove('is-invalid');
            const error = document.getElementById('correo-error');
            if (error) error.remove();
        });

        contrasenaInput.addEventListener('input', () => {
            contrasenaInput.classList.remove('is-invalid');
            const error = document.getElementById('contrasena-error');
            const ojo = document.getElementById('ojo');
            if (error) error.remove();
            if (ojo) ojo.style.display = 'inline';
        });

        const contrasenaError = document.getElementById('contrasena-error');
        const ojo = document.getElementById('ojo');
        if (contrasenaError && ojo) ojo.style.display = 'none';
    });
</script>

</html>
