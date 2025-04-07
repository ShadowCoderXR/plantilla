@extends('layouts.app')

@section('breadcrumb' , 'Administrador')
@section('title' , 'Dashboard')

@push('styles')
@endpush

@section('header')
<!-- Header -->
<div class="container-fluid">
    <div class="page-header min-height-300 border-radius-xl mt-4" style="background-image: url('{{ asset('img/curved-images/curved0.jpg') }}'); background-position-y: 50%;">
        <span class="mask bg-gradient-primary opacity-6" style="background-image: linear-gradient(310deg, #7928CA 0%, #616161 100%);"></span>
    </div>
    <div class="card card-body blur shadow-blur mx-4 mt-n6 overflow-hidden">
        <div class="row gx-4">
            <div class="col">
                <div class="h-100 text-center text-md-start">
                    <h4 class="mb-1">Administradores</h3>
                    <p class="mb-0 text-sm text-muted">Gestión y administración de usuarios con privilegios avanzados.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<section class="py-3">
    <!-- Administradores -->
    <!--
    <div class="row">
        <div class="col-md-8 me-auto text-left">
            <h5>Administradores</h5>
            <p class="mb-0">Lista de administradores que gestionan la plataforma y sus respectivos clientes.</p>
        </div>
    </div> 
    -->
    <div class="row mt-lg-4 mt-2">
        @forelse($administradores as $administrador)
        <div class="col-lg-4 col-md-6 mb-4">
            <a href="{{ route('admin.administrador', $administrador->id) }}" class="text-decoration-none text-dark">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="d-flex">
                            <div class="avatar avatar-xl border-radius-md p-2" style="background-color: {{ $administrador->color }};">
                                <img src="{{ asset('img/favicon.ico') }}" alt="...">
                            </div>
                            <div class="ms-3 my-auto">
                                <h6 class="mb-0">{{ $administrador->nombre }}</h6>
                                <p class="text-xs text-muted"> {{ $administrador->descripcion }} </p>
                            </div>
                        </div>
                        <hr class="horizontal dark">
                        <div class="row">
                            <div class="col-6">
                                <h6 class="text-sm mb-0"> {{ $administrador->numeroClientes }} </h6>
                                <p class="text-secondary text-sm font-weight-bold mb-0">Clientes</p>
                            </div>
                            <div class="col-6 text-end">
                                <h6 class="text-sm mb-0"> {{ $administrador->created_at->format('d-m-Y')  }} </h6>
                                <p class="text-secondary text-sm font-weight-bold mb-0">Fecha de Registro</p>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @empty
        <div class="col-12 text-center my-4">
            <p class="text-muted fs-5">No hay administradores registrados</p>
        </div>
        @endforelse

        {{-- Añadir nuevo administrador--}}
        {{-- <div class="col-lg-4 col-md-6 mb-4">--}}
        {{-- <div class="card h-100">--}}
        {{-- <div class="card-body d-flex flex-column justify-content-center text-center">--}}
        {{-- <a href="javascript:;">--}}
        {{-- <i class="fa fa-plus text-secondary mb-3"></i>--}}
        {{-- <h5 class=" text-secondary"> Nuevo Administrador</h5>--}}
        {{-- </a>--}}
        {{-- </div>--}}
        {{-- </div>--}}
        {{-- </div>--}}
    </div>
</section>
@endsection

@push('scripts')
@endpush