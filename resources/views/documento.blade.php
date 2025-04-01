@extends('layouts.app')

@section('breadcrumb' , 'Administrador')
@section('title' , 'Documento')

@push('styles')
    <style>
        .dropzone.dz-max-files-reached {
            pointer-events: none;
            opacity: 0.6;
            cursor: not-allowed;
        }

        .dropzone.dz-max-files-reached .dz-remove {
            pointer-events: auto;
            cursor: pointer;
            opacity: 1;
        }
    </style>

@endpush

@section('content')
    <div class="row mt-4">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <!-- Encabezado de la tarjeta -->
                <div class="card-header">
                    <h5 class="mb-0">Información del Documento</h5>
                    <p class="text-sm mb-0">
                        Este documento pertenece a <strong>{{ $documentoProveedor->clienteProveedor->proveedor->nombre }}</strong>
                    </p>
                </div>

                <div class="card-body">
                    <div class="row mb-4 d-flex flex-wrap">
                        <!-- Información del Documento -->
                        <div class="col-lg-8 col-12 mb-3 mb-lg-0">
                            <h6 class="text-uppercase text-sm fw-bold text-dark mb-3">Detalles</h6>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <span class="fw-bold d-block">Nombre del Documento</span>
                                    <span class="text-muted">{{$documentoProveedor->documento->nombre}}</span>
                                </li>
                                <li class="list-group-item">
                                    <span class="fw-bold d-block">Estatus</span>
                                    @if ($documentoProveedor->estado == 'cargado')
                                        <span class="badge bg-success">Cargado</span>
                                    @elseif ($documentoProveedor->estado == 'por_cargar')
                                        <span class="badge bg-warning">Por cargar</span>
                                    @elseif ($documentoProveedor->estado == 'faltante')
                                        <span class="badge bg-danger">Faltante</span>
                                    @elseif ($documentoProveedor->estado == 'no_requerido')
                                        <span class="badge bg-secondary">No requerido</span>
                                    @endif
                                </li>
                                <li class="list-group-item">
                                    <span class="fw-bold d-block">Fecha de Carga</span>
                                    <span class="text-muted">
                                        {{ $documentoProveedor->updated_at ? $documentoProveedor->updated_at->translatedFormat('d \d\e F \d\e Y h:i A') : 'No cargado' }}
                                    </span>
                                </li>
                                <li class="list-group-item">
                                    <span class="fw-bold d-block">Ruta de Archivo</span>
                                    <span class="text-muted">
                                        {{ $documentoProveedor->ruta ?? 'No cargado' }}
                                    </span>
                                </li>
                            </ul>
                        </div>

                        @if($documentoProveedor->estado == 'cargado' && $documentoProveedor->ruta)
                            <!-- Botón de Descarga -->
                            <div class="col-lg-4 col-12 d-flex align-items-center justify-content-center">
                                <a class="btn btn-outline-primary w-100 w-lg-auto text-center"
                                   href="{{ route('admin.documento.descargar', $documentoProveedor->id) }}"
                                   style="height: 80px; display: flex; align-items: center; justify-content: center; border-radius: 10px; font-size: 18px;">
                                    <i class="fa fa-download me-2"></i> Descargar Documento
                                </a>
                            </div>
                        @endif

                        @if($documentoProveedor->estado != 'no_requerido')
                            <!-- Dropzone para subir archivo -->
                            <div class="card-body">
                                <h6 class="text-uppercase text-sm fw-bold text-dark mb-3">
                                    @if ($documentoProveedor->estado == 'por_cargar' || $documentoProveedor->estado == 'faltante')
                                        Subir Documento
                                    @else
                                        Actualizar Documento
                                    @endif
                                </h6>
                                <!-- Formulario Dropzone -->
                                <form
                                    action="{{ route('admin.documento.guardar', $documentoProveedor->id) }}"
                                    class="form-control dropzone"
                                    enctype="multipart/form-data"
                                    id="productImg"
                                    method="POST">
                                    @csrf
                                </form>

                                <!-- Botón de envío -->
                                <button id="uploadButton" class="btn btn-outline-primary mt-3 float-end">
                                    <i class="fa fa-upload me-2"></i> Enviar Documento
                                </button>
                            </div>
                       @endif
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
                url: "{{ route('admin.documento.guardar', $documentoProveedor->id) }}",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                acceptedFiles: ".pdf,.xlsx,.xls,.zip,.rar",
                addRemoveLinks: true,
                dictRemoveFile: "Eliminar",
                autoProcessQueue: false,
                uploadMultiple: false,
                parallelUploads: 1,
                maxFiles: 1,
                dictDefaultMessage: "Arrastra aquí el documento o haz clic para subirlo",
                dictInvalidFileType: "Solo se permiten archivos PDF, XLSX, XLS, ZIP y RAR",
                dictMaxFilesExceeded: "Solo puedes subir un archivo",

                init: function () {
                    var myDropzone = this;

                    this.on("addedfile", function (file) {
                        if (this.files.length > 1) {
                            this.removeFile(this.files[0]);
                        }
                    });

                    document.getElementById("uploadButton").addEventListener("click", function () {
                        if (myDropzone.files.length === 0) {
                            alert("Por favor, sube un archivo antes de enviar.");
                        } else {
                            myDropzone.processQueue();
                        }
                    });

                    this.on("success", function (file, response) {
                        alert("Documento subido correctamente");
                        location.reload();
                    });

                    this.on("error", function (file, response) {
                        alert("Hubo un error al subir el archivo: " + response);
                        this.removeFile(file);
                    });
                }
            });
        </script>
    @endpush
