@extends('layouts.app')

@section('breadcrumb' , 'Administrador')
@section('title' , 'Cliente')

@section('back-button' , true)

@push('styles')
@endpush

@section('content')
<div class="row">

    <!-- Card información de la empresa -->
    <div class="col-md-6 d-flex mb-4">
        <div class="card p-0 w-100 d-flex flex-column">
            <div class="row g-0 align-items-center flex-column flex-md-row text-center text-md-start flex-grow-1">
                <div class="col-md-4 d-flex justify-content-center">
                    <div style="/*width: 250px; height: 250px;*/ overflow: hidden; border-radius: 10px;">
                        <img class="img-fluid" src="{{ asset( $cliente->logo ) }}" style="width: 80%; height: 80%; object-fit: contain;" alt="{{ $cliente->nombre }}">
                    </div>
                </div>
                <div class="col-md-8 d-flex align-items-center">
                    <div class="card-body">
                        <h5 class="mb-0"> {{ $cliente->nombre }}</h5>
                        <p class="text-sm mb-3"> Cliente asociado a la empresa <strong> {{$cliente->administradores->first()->nombre}} </strong> </p>
{{--                        <h6 class="text-uppercase text-sm mb-3">Información de la Empresa</h6>--}}
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex align-items-center ps-0">
                                <i class="fas fa-phone-alt me-2 text-primary"></i>
                                <span class="fw-bold">Teléfono:</span>&nbsp;{{ $cliente->telefono }}
                            </li>
                            <li class="list-group-item d-flex align-items-center ps-0">
                                <i class="fas fa-envelope me-2 text-danger"></i>
                                <span class="fw-bold">Correo:</span>&nbsp;{{ $cliente->correo }}
                            </li>
                            <li class="list-group-item d-flex align-items-center ps-0">
                                <i class="fas fa-calendar-alt me-2 text-success"></i>
                                <span class="fw-bold">Fecha de Registro:</span>&nbsp;{{ $cliente->created_at->format('d/m/Y') }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Descripción Adicional -->
    <div class="col-md-6 d-flex mb-4">
        <div class="card w-100 d-flex flex-column h-100">
            <div class="card-body">
                <h5 class="text-uppercase mb-2">Descripción Adicional</h6>
                    <p class="text-muted mb-0" style="font-size: 15px;">
                        {{ $cliente->descripcion_adicional }}
                    </p>
                </h5>
            </div>

            <hr class="horizontal dark my-0">

            <div class="card-footer bg-white border-0">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="infoTipo" class="text-muted small mb-0">
                            Selecciona los documentos a descargar:
                        </label>
                    </div>
                    <div class="col-md-4">
                        <select id="infoTipo"
                            class="form-select form-select-sm w-auto"
                            style="min-width: 180px;">
                            <option value="1">Todos los documentos</option>
                            <option value="2">Documentos por Año</option>
                            <option value="3">Documentos por Mes</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <!-- Cambiamos la clase del botón -->
                        <a class="btn btn-sm bg-gradient-primary btnDescargarDocumentos mb-0"
                            style="white-space: nowrap;">
                            <i class="fa fa-download me-2"></i> Descargar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para el primer caso (modalSeleccion) -->
    <div class="modal fade" id="modalSeleccion" tabindex="-1" aria-labelledby="modalSeleccionLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header">
                    <h6 class="modal-title d-flex align-items-center gap-2 text-dark fw-bold" id="modalSeleccionLabel">
                        <i class="fa fa-info-circle fa-lg text-dark" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.1);"></i>
                        Confirmar Descarga
                    </h6>
                    <button type="button" class="btn p-0 border-0 bg-transparent ms-auto" data-bs-dismiss="modal"
                        aria-label="Cerrar" style="box-shadow: none;">
                        <i class="fa fa-times fa-lg text-secondary"></i>
                    </button>
                </div>
                <div class="modal-body pt-2 pb-2 px-3">
                    <p id="mensajeConfirmacion" class="text-center text-secondary small my-3">
                        Se descargarán los documentos de todos los clientes y proveedores del periodo seleccionado.
                    </p>
                    <form id="formSeleccion" method="POST" action="{{ route('documentos.zip') }}">
                        @csrf
                        <div id="grupoMes" class="mb-3" style="display: none;">
                            <label for="mesSeleccionado" class="form-label text-sm">Mes</label>
                            <select id="mesSeleccionado" class="form-select rounded-2 shadow-sm">
                                @foreach(range(1, 12) as $mes)
                                <option value="{{ $mes }}">{{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="grupoAnio" class="mb-3" style="display: none;">
                            <label for="anioSeleccionado" class="form-label text-sm">Año</label>
                            <select id="anioSeleccionado" class="form-select rounded-2 shadow-sm">
                                @foreach ($anios as $anio)
                                <option value="{{ $anio }}">{{ $anio }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn bg-gradient-primary w-100 rounded-2 shadow-sm" data-bs-dismiss="modal">
                                <i class="fa fa-check me-2"></i> Confirmar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Card proveedores -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Proveedores</h5>
                <p class="text-sm">Listado de proveedores asociados a la empresa.</p>
            </div>
            <div class="card-body pt-0">
                @forelse($cliente->proveedores as $proveedor)
                <div class="d-sm-flex bg-gray-100 border-radius-lg p-2 mb-4">
                    <img src="{{ asset($proveedor->logo) }}" alt="{{ $proveedor->nombre }}" class="img-fluid rounded-circle object-fit-contain"
                        style="width: 48px; height: 48px;">
                    <div class="my-auto ms-3">
                        <h5 class="mb-0 text-sm">{{ $proveedor->nombre }}</h5>
                    </div>
                    <div class="ms-auto d-flex align-items-center">
                        <a class="btn btn-sm bg-gradient-primary btnProveedorDocumentos mb-0"
                           data-proveedor-id="{{ $proveedor->id }}"
                           data-cliente-id="{{ $cliente->id }}">

                            Documentos
                        </a>
                    </div>
                </div>
                @empty
                <div class="text-center">
                    <h5>No hay proveedores asociados a esta empresa.</h5>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDocumentos" tabindex="-1" aria-labelledby="modalDocumentosLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header">
                <h6 class="modal-title d-flex align-items-center gap-2 text-dark fw-bold" id="modalDocumentosLabel">
                    <i class="fa fa-folder-open fa-lg text-dark" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.1);"></i>
                    Selecciona Año
                </h6>
                <button type="button" class="btn p-0 border-0 bg-transparent ms-auto" data-bs-dismiss="modal" aria-label="Cerrar" style="box-shadow: none;">
                    <i class="fa fa-times fa-lg text-secondary"></i>
                </button>
            </div>
            <div class="modal-body pt-2 pb-2 px-3">
                <form id="formDocumentos">
                    <input type="hidden" id="inputProveedorId" name="idProveedor">
                    <input type="hidden" id="inputClienteId" name="idCliente">

                    <div class="mb-3">
                        <label for="modalAnioSelector" class="form-label text-sm">Año</label>
                        <select id="modalAnioSelector" class="form-select rounded-2 shadow-sm">
                            @foreach($anios as $anio)
                            <option value="{{ $anio }}">{{ $anio }}</option>
                            @endforeach
                        </select>

                        <label for="modalTipoDocumentoSelector" class="form-label text-sm mt-3">Tipo de Documento</label>
                        <select id="modalTipoDocumentoSelector" class="form-select rounded-2 shadow-sm">
                            @foreach($tiposDocumentos as $tipo)
                            <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn bg-gradient-primary w-100 rounded-2 shadow-sm">
                            <i class="fa fa-check me-2"></i> Confirmar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".btnProveedorDocumentos").forEach(btn => {
            btn.addEventListener("click", function() {
                const proveedorId = this.getAttribute("data-proveedor-id");
                const clienteId = this.getAttribute("data-cliente-id");
                document.getElementById("inputProveedorId").value = proveedorId;
                document.getElementById("inputClienteId").value = clienteId;

                const modal = new bootstrap.Modal(document.getElementById("modalDocumentos"));
                modal.show();
            });
        });

        const form = document.getElementById("formDocumentos");
        form.addEventListener("submit", function(e) {
            e.preventDefault();

            const proveedorId = document.getElementById("inputProveedorId").value;
            const clienteId = document.getElementById("inputClienteId").value;
            const anio = document.getElementById("modalAnioSelector").value;
            const tipo = document.getElementById("modalTipoDocumentoSelector").value;

            const url = "{{ route('admin.proveedor', ['idProveedor' => '__IDPROVEEDOR__', 'idCliente' => '__IDCLIENTE__','año' => '__YEAR__', 'tipo' => '__TIPO__']) }}"
                .replace('__IDPROVEEDOR__', proveedorId)
                .replace('__IDCLIENTE__', clienteId)
                .replace('__YEAR__', anio)
                .replace('__TIPO__', tipo);

            window.location.href = url;
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const tipoSelect = document.getElementById("infoTipo");
        const btnDescargar = document.querySelector(".btnDescargarDocumentos");

        const modalSeleccion = new bootstrap.Modal(document.getElementById("modalSeleccion"));
        const grupoMes = document.getElementById("grupoMes");
        const grupoAnio = document.getElementById("grupoAnio");

        btnDescargar.addEventListener("click", function(e) {
            e.preventDefault();

            grupoMes.style.display = "none";
            grupoAnio.style.display = "none";

            const tipo = tipoSelect.value;
            if (tipo === "2") {
                grupoAnio.style.display = "block";
            }
            if (tipo === "3") {
                grupoMes.style.display = "block";
                grupoAnio.style.display = "block";
            }

            modalSeleccion.show();
        });
    });
</script>

<script>
    window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
            document.querySelectorAll('.modal.show').forEach(modal => {
                const instance = bootstrap.Modal.getInstance(modal) || new bootstrap.Modal(modal);
                instance.hide();
            });

            document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());

            document.querySelectorAll('.modal').forEach(modal => {
                modal.classList.remove('show');
                modal.style.display = 'none';
                modal.setAttribute('aria-hidden', 'true');
            });
        }
    });
</script>
@endpush
