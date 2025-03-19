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
                            <img class="border-radius-lg shadow-sm img-fluid" src="{{ asset( $cliente->logo ) }}" style="width: 100%; height: 100%; object-fit: contain;">
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
                                    <span class="fw-bold">Teléfono:</span> {{ $cliente->telefono }}
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="fas fa-envelope me-2 text-danger"></i>
                                    <span class="fw-bold">Correo:</span> {{ $cliente->correo }}
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="fas fa-calendar-alt me-2 text-success"></i>
                                    <span class="fw-bold">Fecha de Registro:</span> {{ $cliente->created_at }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card información adicional -->
        <div class="col-md-6 d-flex mb-4">
            <div class="card p-3 w-100 d-flex flex-column">
                <div class="card-body flex-grow-1">
                    <h5 class="mb-3">Descripción Adicional</h5>
                    <p class="text-muted">
                        {{ $cliente->descripcion_adicional }}
                    </p>
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
                            <img class="width-48-px" src="{{ asset($proveedor->logo) }}">
                            <div class="my-auto ms-3">
                                <h5 class="mb-0">{{ $proveedor->nombre }}</h5>
                                <p class="text-sm text-muted mb-1">{{ $proveedor->descripcion }}</p>
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
@endpush
