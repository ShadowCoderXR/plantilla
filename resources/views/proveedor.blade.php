@extends('layouts.app')

@section('breadcrumb' , 'Administrador')
@section('title' , 'Proveedor')

@push('styles')
@endpush

@section('content')
    <div class="row">
        <!-- Descripción de la Empresa -->
        <div class="col-md-6 d-flex mb-4">
            <div class="card p-3 w-100 d-flex flex-column">
                <div class="row g-0 align-items-center flex-column flex-md-row text-center text-md-start flex-grow-1">
                    <div class="col-md-4 d-flex justify-content-center">
                        <div style="width: 250px; height: 250px; overflow: hidden; border-radius: 10px;">
                            <img alt="{{ $proveedor->nombre }}" class="border-radius-lg shadow-sm img-fluid" src="{{ asset($proveedor->logo) }}" style="width: 100%; height: 100%; object-fit: contain;">
                        </div>
                    </div>
                    <div class="col-md-8 d-flex align-items-center">
                        <div class="card-body">
                            <h5 class="mb-0">{{ $proveedor->nombre }}</h5>
                            <p class="text-sm mb-3 text-muted">{{ $proveedor->descripcion }}</p>
                            <h6 class="text-uppercase text-sm mb-3">Información de la Empresa</h6>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="fas fa-phone-alt me-2 text-primary"></i>
                                    <span class="fw-bold">Teléfono:</span>&nbsp;{{ $proveedor->telefono }}
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="fas fa-envelope me-2 text-danger"></i>
                                    <span class="fw-bold">Correo:</span>&nbsp;{{ $proveedor->correo }}
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="fas fa-calendar-alt me-2 text-success"></i>
                                    <span class="fw-bold">Fecha de Registro:</span>&nbsp;{{ $proveedor->created_at->format('d/m/Y') }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Descripción Adicional -->
        <div class="col-md-6 d-flex mb-4">
            <div class="card w-100 d-flex flex-column h-100">
                <div class="card-body">
                    <h6 class="text-uppercase text-sm mb-2">Descripción Adicional</h6>
                    <p class="text-muted mb-0" style="font-size: 15px;">
                        {{ $proveedor->descripcion_adicional }}
                    </p>
                </div>

                <hr class="horizontal dark my-0">

                <div class="card-footer bg-white border-0">
                    <div class="d-flex justify-content-end align-items-center flex-wrap gap-2">
                        <label for="infoTipo" class="text-muted small mb-0">
                            Selecciona los documentos a descargar:
                        </label>

                        <select id="infoTipo"
                                class="form-select form-select-sm w-auto"
                                style="min-width: 180px;">
                            <option value="1">Todos los documentos</option>
                            <option value="2">Documentos por Año</option>
                            <option value="3">Documentos por Mes</option>
                        </select>

                        <a class="btn btn-sm bg-gradient-primary documentosLink mb-0"
                           href="#"
                           style="white-space: nowrap;">
                            <i class="fa fa-download me-2"></i> Descargar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                        Se descargarán todos los documentos del periodo seleccionado.
                    </p>

                    <form>
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
                                @foreach ($anios as $a)
                                    <option value="{{ $a }}">{{ $a }}</option>
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

    <!-- Tabla de Documentos -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Documentos</h5>
                        <p class="text-sm mb-0">Documentos requeridos para la inscripción</p>
                    </div>
                    <div class="mt-3 content-center text-center">
                        <ul class="list-inline">
                            <li class="list-inline-item me-3 text-bold"> Simbología: </li>
                            <li class="list-inline-item me-3">
                                <i class="fa fa-check text-success"></i> Cargado
                            </li>
                            <li class="list-inline-item me-3">
                                <i class="fa fa-upload text-warning"></i> Por cargar
                            </li>
                            <li class="list-inline-item">
                                <i class="fa fa-times text-danger"></i> Faltante
                            </li>
                            <li class="list-inline-item">
                                <i class="fa fa-minus text-black-50"></i> No requerido
                            </li>
                        </ul>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="me-2 text-sm">Año:</span>
                        <select class="form-select form-select-lg yearSelector" style="width: auto; min-width: 150px;">
                            @foreach ($anios as $a)
                                <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    @foreach (collect($resultado)->groupBy('grupo') as $grupo => $documentos)
                        <div class="d-flex justify-content-between align-items-center py-2">
                            <div class="my-auto ms-3">
                                <h6 class="mb-0">{{ $grupo }}</h6>
                                <p class="text-sm text-muted mb-1">{{ $grupos[$grupo]->descripcion ?? 'Sin descripción' }}</p>
                            </div>

                            <a href="#" class="text-sm toggle-table ms-auto" data-target="grupo-{{ Str::slug($grupo) }}">
                                Mostrar <i class="fas fa-chevron-down text-xs ms-1"></i>
                            </a>
                        </div>
                        <div class="table-responsive collapse-group py-2" id="grupo-{{ Str::slug($grupo) }}" style="display: none;">
                            <table class="table table-hover align-items-center datatable" id="datatable-{{ Str::slug($grupo) }}">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Documento</th>
                                        @foreach(range(1, 12) as $mes)
                                            <th>{{ \Carbon\Carbon::create()->month($mes)->translatedFormat('M') }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($documentos as $fila)
                                        <tr>
                                            <td>{{ $fila['documento'] }}</td>
                                            @foreach ($fila['meses'] as $mes => $info)
                                                <td class="status-{{ $info['estado'] }}">
                                                    @switch($info['estado'])
                                                        @case('cargado')
                                                            <a href="{{ route('admin.documento', $info['id']) }}"><i class="fa fa-check text-success"></i></a>
                                                            @break

                                                        @case('por_cargar')
                                                            <a href="{{ route('admin.documento', $info['id']) }}"><i class="fa fa-upload text-warning"></i></a>
                                                            @break

                                                        @case('faltante')
                                                            <a href="{{ route('admin.documento', $info['id']) }}"><i class="fa fa-times text-danger"></i></a>
                                                            @break

                                                        @default
                                                            <i class="fa fa-minus text-black-50"></i>
                                                    @endswitch
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <hr class="horizontal dark my-0">
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".datatable").forEach((table) => {
                new simpleDatatables.DataTable(table, {
                    searchable: false,
                    paging: false,
                    sortable: false,
                    template: (options, dom) => `
                    <div class='${options.classes.container}'>
                        ${dom.table.outerHTML}
                    </div>
                `
                });
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".toggle-table").forEach(button => {
                button.addEventListener("click", function () {
                    const target = document.getElementById(this.dataset.target);
                    const visible = target.style.display === "block";
                    target.style.display = visible ? "none" : "block";
                    this.innerHTML = visible
                        ? "Mostrar <i class='fas fa-chevron-down text-xs ms-1'></i>"
                        : "Ocultar <i class='fas fa-chevron-up text-xs ms-1'></i>";
                });
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelector(".yearSelector").addEventListener("change", function () {

                console.log("Cambio detectado");

                const selectedYear = this.value;
                const providerId = "{{ $proveedor->id }}";

                window.location.href = `{{ route('admin.proveedor', ['id' => $proveedor->id, 'año' => '__YEAR__']) }}`.replace('__YEAR__', selectedYear);
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const tipoSelect = document.getElementById("infoTipo");
            const btnDescargar = document.querySelector(".documentosLink");

            const modal = new bootstrap.Modal(document.getElementById("modalSeleccion"));
            const grupoMes = document.getElementById("grupoMes");
            const grupoAnio = document.getElementById("grupoAnio");
            const mensajeConfirmacion = document.getElementById("mensajeConfirmacion");

            btnDescargar.addEventListener("click", function (e) {
                e.preventDefault();

                const tipo = tipoSelect.value;

                grupoMes.style.display = "none";
                grupoAnio.style.display = "none";

                if (tipo === "2") {
                    grupoAnio.style.display = "block";
                }

                if (tipo === "3") {
                    grupoMes.style.display = "block";
                    grupoAnio.style.display = "block";
                }

                modal.show();
            });
        });
    </script>
@endpush


