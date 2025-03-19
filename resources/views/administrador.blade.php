@extends('layouts.app')

@section('breadcrumb' , 'Administrador')
@section('title' , 'Administrador')

@push('styles')
@endpush

@section('content')
    <div class="row">
        <!-- Card Información de la Empresa -->
        <div class="col-md-6 d-flex mb-4">
            <div class="card p-3 w-100 d-flex flex-column">
                <div class="row g-0 align-items-center flex-column flex-md-row text-center text-md-start flex-grow-1">
                    <div class="col-md-4 d-flex justify-content-center">
                        <div style="width: 250px; height: 250px; overflow: hidden; border-radius: 10px;">
                            <img alt="vp360" class="border-radius-lg shadow-sm img-fluid" src="{{ asset($administrador->logo) }}" style="width: 100%; height: 100%; object-fit: contain;">
                        </div>
                    </div>
                    <div class="col-md-8 d-flex align-items-center">
                        <div class="card-body">
                            <h5 class="mb-0">{{ $administrador->nombre }}</h5>
                            <p class="text-sm mb-3 text-muted">{{ $administrador->descrpcion }}</p>
                            <h6 class="text-uppercase text-sm mb-3">Información de la Empresa</h6>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="fas fa-phone-alt me-2 text-primary"></i>
                                    <span class="fw-bold">Teléfono:</span> {{ $administrador->telefono }}
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="fas fa-envelope me-2 text-danger"></i>
                                    <span class="fw-bold">Correo:</span> {{ $administrador->correo }}
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="fas fa-calendar-alt me-2 text-success"></i>
                                    <span class="fw-bold">Fecha de Registro:</span> {{ $administrador->created_at->format('d/m/Y') }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Descripción Adicional -->
        <div class="col-md-6 d-flex mb-4">
            <div class="card p-3 w-100 d-flex flex-column">
                <div class="card-body flex-grow-1">
                    <h5 class="mb-3">Descripción Adicional</h5>
                    <p class="text-muted">
                        {{ $administrador->descripcion_adicional }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Clientes -->
    <div class="row">
        <div class="col-12">
            <div class="card" id="clientes">
                <div class="card-header">
                    <h5>Clientes</h5>
                    <p class="text-sm">Listado de clientes registrados en el sistema</p>
                </div>

                @forelse($administrador->clientes as $cliente)
                    <div class="card-body pt-0" id="clients-container">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex">
                                <img class="width-48-px" src="{{ asset( $cliente->logo ) }}">
                                <div class="my-auto ms-3">
                                    <a href="{{ route('admin.cliente', ['id' => $cliente->id]) }}"><h5 class="mb-0">{{ $cliente->nombre }}</h5></a>
                                    <p class="text-sm text-muted mb-1">{{ $cliente->descripcion }}</p>
                                </div>
                            </div>
                            <a href="#" class="text-sm text-body toggle-providers ms-auto">Ver más <i class="fas fa-chevron-down text-xs ms-1"></i></a>
                        </div>
                        <div class="ps-5 pt-3 ms-3 providers-list" style="display: none;">
                            <h6 class="text-uppercase text-sm mb-3">Proveedores</h6>
                            @forelse($cliente->proveedores as $proveedor)
                                <div class="d-sm-flex bg-gray-100 border-radius-lg p-2 my-4">
                                    <img class="width-48-px" src="{{ asset($proveedor->logo) }}">
                                    <div class="ms-3">
                                        <h6 class="mb-0">{{ $proveedor->nombre }}</h6>
                                        <p class="mb-0 text-sm">{{ $proveedor->descripcion }}</p>
                                    </div>
                                    <div class="ms-auto d-flex align-items-center">
                                        <select class="form-select form-select-sm me-2 yearSelector" style="width: auto; min-width: 80px;">
                                            <option value="2025">2025</option>
                                            <option value="2024">2024</option>
                                            <option value="2023">2023</option>
                                            <option value="2022">2022</option>
                                            <option value="2021">2021</option>
                                        </select>
                                        <a href="{{ route('admin.proveedor', ['id' => $proveedor->id, 'año' => '2024']) }}" class="btn btn-sm bg-gradient-primary my-sm-auto mt-2 mb-0">Documentos</a>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted">No hay proveedores registrados para este cliente.</p>
                            @endforelse
                        </div>
                        <hr class="horizontal dark">
                    </div>
                @empty
                    <div class="text-center p-4">
                        <p class="text-muted">No hay clientes registrados.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Script para mostrar/ocultar los proveedores de un cliente -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".toggle-providers").forEach(button => {
                button.addEventListener("click", function (event) {
                    event.preventDefault();
                    let providersList = this.parentElement.nextElementSibling;
                    if (providersList && providersList.classList.contains("providers-list")) {
                        let isVisible = providersList.style.display === "block";
                        providersList.style.display = isVisible ? "none" : "block";
                        this.innerHTML = isVisible
                            ? "Ver más <i class='fas fa-chevron-down text-xs ms-1'></i>"
                            : "Ver menos <i class='fas fa-chevron-up text-xs ms-1'></i>";
                    }
                });
            });
        });
    </script>
@endpush
