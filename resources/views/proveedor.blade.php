@extends('layouts.app')

@section('breadcrumb' , 'Administrador')
@section('title' , 'Proveedor')

@push('styles')
    <style>
        .text-warning {
            color: #FBB03C !important;
        }

        .badge-primary {
            color: #ffffff;
            background-color: #344767;
        }
    </style>
@endpush


@section('content')
<div class="row">
    <!-- Descripción de la Empresa -->
    <div class="col-md-6 d-flex mb-4">
        <div class="card p-0 w-100 d-flex flex-column">
            <div class="row g-0 align-items-center flex-column flex-md-row text-center text-md-start flex-grow-1">
                <div class="col-md-4 d-flex justify-content-center">
                    <div style="width: 250px; height: 250px; overflow: hidden; border-radius: 10px;">
                        <img alt="{{ $proveedor->nombre }}" class="border-radius-lg shadow-sm img-fluid" src="{{ asset($proveedor->logo) }}" style="width: 100%; height: 100%; object-fit: contain;">
                    </div>
                </div>
                <div class="col-md-8 d-flex align-items-center">
                    <div class="card-body">
                        <h5 class="mb-0">{{ $proveedor->nombre }}</h5>
                        <p class="text-sm mb-3 text-white">{{ $proveedor->descripcion }}</p>
                        <!-- <h6 class="text-uppercase text-sm mb-3">Información de la Empresa</h6> -->
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex align-items-center ps-0">
                                <i class="fas fa-phone-alt me-2 text-primary"></i>
                                <span class="fw-bold">Teléfono:</span>&nbsp;{{ $proveedor->telefono }}
                            </li>
                            <li class="list-group-item d-flex align-items-center ps-0">
                                <i class="fas fa-envelope me-2 text-danger"></i>
                                <span class="fw-bold">Correo:</span>&nbsp;{{ $proveedor->correo }}
                            </li>
                            <li class="list-group-item d-flex align-items-center ps-0">
                                <i class="fas fa-calendar-alt me-2 text-success"></i>
                                <span class="fw-bold">Fecha de Registro:</span>&nbsp;{{ $proveedor->created_at->format('d/m/Y') }}
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
                        {{ $proveedor->descripcion_adicional }}
                    </p>
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
                            href="#"
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
                                @foreach ($anios as $a)
                                <option value="{{ $a }}">{{ $a }}</option>
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

<!-- Tabla de Documentos -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Documentos <span class="text-bold">{{ $tipoDocumento->nombre }}</span></h5>
                    <p class="text-sm mb-0">Documentos requeridos para la inscripción</p>
                </div>
                <div class="mt-3 content-center text-center">
                    <ul class="list-inline">
                        <li class="list-inline-item me-3 text-bold"> Simbología: </li>
                        <li class="list-inline-item me-3">
                            <i class="fa fa-check-circle text-success"></i> Cargado
                        </li>
                        <li class="list-inline-item me-3">
                            <i class="fa fa-cloud-upload-alt text-warning"></i> Por cargar
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
                    <span class="me-2 text-bold">Año:</span>
                    <select class="form-select form-select-sm yearSelector" style="width: auto; min-width: 150px;">
                        @foreach ($anios as $a)
                        <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @php
                /** @var \Illuminate\Support\Collection $resultado */
                $grupos = collect($resultado)->groupBy('grupo');
                $hayMultiplesGrupos = $grupos->count() > 1;            
            @endphp

            <!-- Documentos Única Vez-->
            <div class="col-md-4 ps-4">
                <div class="card-body bg-gray-100 border-radius-lg pb-0">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="infoTipo" class="small mb-0 ms-0 badge badge-primary">
                                Documentos (Única vez)
                            </label>
                        </div>
                        @foreach ($resultadoUnicaVez as $key => $documento)
                            <div class="col-md-9 ps-4">
                                <p class="text-sm text-bold" style="color: #27272a">{{ $documento['documento']['nombre'] }}</p>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('admin.documento', [$documento->id, 'uv']) }}"><i class="fa {{ $documento->estado == 'por_cargar' ? 'fa-cloud-upload-alt text-warning' : 'fa-check-circle text-success' }}"></i></a>
                            </div>                              
                        @endforeach                
                    </div>
                </div>
            </div>

            <div class="card-body pt-0">
                @foreach ($grupos as $grupo => $documentos)
                    @if($hayMultiplesGrupos)
                        <div class="d-flex justify-content-between align-items-center py-2">
                            <div class="my-auto ms-3">
                                <h6 class="mb-3">{{ $grupo }}</h6>
                            </div>
                            <a href="#" class="text-sm toggle-table ms-auto" data-target="grupo-{{ Str::slug($grupo) }}">
                                Mostrar <i class="fas fa-chevron-down text-xs ms-1"></i>
                            </a>
                        </div>
                    @endif

                    <div class="table-responsive {{ $hayMultiplesGrupos ? 'collapse-group py-2' : 'py-2' }}"
                        id="grupo-{{ Str::slug($grupo) }}"
                        style="{{ $hayMultiplesGrupos ? 'display: none;' : '' }}">
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
                                    @if (!str_contains($fila['informacion'], 'Documento única vez'))
                                        <tr>
                                            <td class="text-sm text-bold" 
                                                style="{{ str_contains($fila['documento'], '- ISR') || str_contains($fila['documento'], '- IVA') ? 'padding-left: 3rem' : 'color: #27272a; white-space: normal; width: 26%' }}"
                                                @if($fila['informacion'])
                                                title="{{ $fila['informacion'] }}"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="left"
                                                @endif>
                                                {{ $fila['documento'] }}
                                            </td>
                                            @foreach ($fila['meses'] as $mes => $info)
                                                <td class="status-{{ $info['estado'] }}">
                                                    @switch($info['estado'])
                                                        @case('cargado')
                                                            <a href="{{ route('admin.documento', $info['id']) }}"><i class="fa fa-check-circle text-success"></i></a>
                                                        @break
                                                        @case('por_cargar')
                                                            <a href="{{ route('admin.documento', $info['id']) }}"><i class="fa fa-cloud-upload-alt text-warning"></i></a>
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
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($hayMultiplesGrupos)
                        <hr class="horizontal dark my-0">
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
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
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".toggle-table").forEach(button => {
            button.addEventListener("click", function() {
                const target = document.getElementById(this.dataset.target);
                const visible = target.style.display === "block";
                target.style.display = visible ? "none" : "block";
                this.innerHTML = visible ?
                    "Mostrar <i class='fas fa-chevron-down text-xs ms-1'></i>" :
                    "Ocultar <i class='fas fa-chevron-up text-xs ms-1'></i>";
            });
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelector(".yearSelector").addEventListener("change", function() {

            console.log("Cambio detectado");

            const selectedYear = this.value;
            const providerId = "{{ $proveedor->id }}";
            const tipo = "{{ $tipoDocumento->id }}";

            window.location.href = `{{ route('admin.proveedor', ['id' => $proveedor->id, 'año' => '__YEAR__', 'tipo' => '__TIPO__']) }}`
                .replace('__YEAR__', selectedYear)
                .replace('__TIPO__', tipo);
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
    document.addEventListener('DOMContentLoaded', function() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.forEach(function(tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>

@endpush