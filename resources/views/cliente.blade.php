@extends('layouts.app')

@section('breadcrumb' , 'Administrador')
@section('title' , 'Cliente')

@push('styles')
@endpush

@section('content')
    <div class="row">

        <!-- Card información de la empresa -->
        <div class="col-md-6 d-flex mb-4">
            <div class="card p-3 w-100 d-flex flex-column">
                <div class="row g-0 align-items-center flex-column flex-md-row text-center text-md-start flex-grow-1">
                    <div class="col-md-4 d-flex justify-content-center">
                        <div style="width: 250px; height: 250px; overflow: hidden; border-radius: 10px;">
                            <img class="border-radius-lg shadow-sm img-fluid" src="{{ asset( $cliente->logo ) }}" style="width: 100%; height: 100%; object-fit: contain;" alt="{{ $cliente->nombre }}">
                        </div>
                    </div>
                    <div class="col-md-8 d-flex align-items-center">
                        <div class="card-body">
                            <h5 class="mb-0"> {{ $cliente->nombre }} </h5>
                            <p class="text-sm mb-3 text-muted"> {{ $cliente->descripcion }} </p>
                            <h6 class="text-uppercase text-sm mb-3">Información de la Empresa</h6>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="fas fa-phone-alt me-2 text-primary"></i>
                                    <span class="fw-bold">Teléfono:</span>&nbsp;{{ $cliente->telefono }}
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="fas fa-envelope me-2 text-danger"></i>
                                    <span class="fw-bold">Correo:</span>&nbsp;{{ $cliente->correo }}
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="fas fa-calendar-alt me-2 text-success"></i>
                                    <span class="fw-bold">Fecha de Registro:</span>&nbsp;{{ $cliente->created_at->format('d/m/Y') }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card información adicional -->
        <div class="col-md-6 d-flex mb-4">
            <div class="card w-100 d-flex flex-column h-100">
                <div class="card-body">
                    <h6 class="text-uppercase text-sm mb-2">Descripción Adicional</h6>
                    <p class="text-muted mb-0" style="font-size: 15px;">
                        {{ $cliente->descripcion_adicional }}
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
                        Se descargarán los documentos de todos los proveedores del periodo seleccionado.
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
                                @foreach ($anios as $anio)
                                    <option value="{{ $anio }}">{{ $anio }}</option>
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

    <!-- Card proveedores -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Proveedores</h5>
                    <p class="text-sm">Listado de proveedores asociados a la empresa.</p>
                </div>
                <div class="card-body pt-0" >
                    @forelse($cliente->proveedores as $proveedor)
                        <div class="d-sm-flex bg-gray-100 border-radius-lg p-2 mb-4">
                            <img src="{{ asset($proveedor->logo) }}" alt="{{ $proveedor->nombre }}" class="img-fluid rounded-circle object-fit-contain"
                                 style="width: 48px; height: 48px;">
                            <div class="my-auto ms-3">
                                <h5 class="mb-0">{{ $proveedor->nombre }}</h5>
                                <p class="text-sm text-muted mb-1">{{ $proveedor->descripcion }}</p>
                            </div>
                            <div class="ms-auto d-flex align-items-center">
                                <select class="form-select form-select-sm me-2 yearSelector" style="width: auto; min-width: 80px;" id="yearSelector">
                                    @foreach ($anios as $anio)
                                        <option value="{{ $anio }}">{{ $anio }}</option>
                                    @endforeach
                                </select>
                                <a href="{{ route('admin.proveedor', ['id' => $proveedor->id, 'año' => 2025]) }}"
                                   class="btn btn-sm bg-gradient-primary my-sm-auto mt-2 mb-0"
                                   id="documentosLink">
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
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const yearSelector = document.getElementById("yearSelector");
            const documentosLink = document.getElementById("documentosLink");

            yearSelector.addEventListener("change", function () {
                const selectedYear = this.value;
                documentosLink.href = "{{ route('admin.proveedor', ['id' => $proveedor->id, 'año' => '__YEAR__']) }}".replace('__YEAR__', selectedYear);
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
