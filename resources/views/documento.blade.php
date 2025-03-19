@extends('layouts.app')

@section('breadcrumb' , 'Administrador')
@section('title' , 'Documento')

@push('styles')
@endpush

@section('content')
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <!-- Encabezado de la tarjeta -->
                <div class="card-header">
                    <h5 class="mb-0">Información del Documento</h5>
                    <p class="text-sm mb-0">Este documento ha sido cargado correctamente</p>
                </div>

                <div class="card-body">
                    <div class="row mb-4 d-flex flex-wrap">
                        <!-- Información del Documento -->
                        <div class="col-lg-8 col-12 mb-3 mb-lg-0">
                            <h6 class="text-uppercase text-sm fw-bold text-dark mb-3">Detalles</h6>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <span class="fw-bold d-block">Nombre del Documento</span>
                                    <span class="text-muted">Comprobante de Pago de Cuotas</span>
                                </li>
                                <li class="list-group-item">
                                    <span class="fw-bold d-block">Estatus</span>
                                    @if ($status == 1)
                                        <span class="badge bg-success">Cargado</span>
                                    @elseif ($status == 2)
                                        <span class="badge bg-warning">Cargando</span>
                                    @elseif ($status == 3)
                                        <span class="badge bg-danger">Faltante</span>
                                    @endif
                                </li>
                                <li class="list-group-item">
                                    <span class="fw-bold d-block">Fecha de Carga</span>
                                    <span class="text-muted">2024/04/10</span>
                                </li>
                                <li class="list-group-item">
                                    <span class="fw-bold d-block">Tipo de Archivo</span>
                                    <span class="text-muted">PDF</span>
                                </li>
                            </ul>
                        </div>

                        @if($status == 1 || $status == 2)
                            <!-- Botón de Descarga -->
                            <div class="col-lg-4 col-12 d-flex align-items-center justify-content-center">
                                <a class="btn btn-outline-primary w-100 w-lg-auto text-center"
                                   download
                                   href="ruta-del-archivo/comprobante_pago.pdf"
                                   style="height: 80px; display: flex; align-items: center; justify-content: center; border-radius: 10px; font-size: 18px;">
                                    <i class="fa fa-download me-2"></i> Descargar Documento
                                </a>
                            </div>
                        @endif

                        @if ($status == 3 || $status == 2)
                            <!-- Dropzone para subir archivo -->
                            <div class="card-body">
                                <h6 class="text-uppercase text-sm fw-bold text-dark mb-3">
                                    @if ($status == 3)
                                        Subir Documento
                                    @else
                                        Actualizar Documento
                                    @endif
                                </h6>
                                <!-- Formulario Dropzone -->
                                <form
                                    action=""
                                    class="form-control dropzone"
                                    enctype="multipart/form-data"
                                    id="productImg"
                                    method="POST">
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Dropzone -->
    <script>
        Dropzone.autoDiscover = false;

        var productImg = new Dropzone("#productImg", {
            url: "#",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            addRemoveLinks: true,
            dictRemoveFile: "Eliminar",
            autoProcessQueue: false,
            uploadMultiple: false,
            parallelUploads: 1,
            maxFiles: 1,
            dictDefaultMessage: "Arrastra aquí el documento o haz clic para subirlo",
        });
    </script>
@endpush
