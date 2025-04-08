@extends('layouts.app')

@section('breadcrumb' , 'Administrador')
@section('title' , 'Documento')

@push('styles')
    <style>
        .badge.bg-warning {
            background: #FBB03C !important;
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
                                    <span class="text-muted">{{ $documentoProveedor->documento->nombre}}</span>
                                </li>


                                @if($documentoProveedor->documento->informacion && $documentoProveedor->documento->informacion != 'Documento única vez')
                                    <li class="list-group-item">
                                        <span class="fw-bold d-block">Información</span>
                                        <span class="text-muted">{{ $documentoProveedor->documento->informacion }}</span>
                                    </li>
                                @endif
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
                                        {{ $documentoProveedor->fecha_carga ? \Carbon\Carbon::parse($documentoProveedor->fecha_carga)->translatedFormat('d \d\e F \d\e Y h:i A') : 'No cargado' }}
                                    </span>
                                </li>
                                @if(isset($archivos) && count($archivos) > 0)
                                    <li class="list-group-item">
                                        <span class="fw-bold d-block">Archivos en carpeta</span>
                                        <ul class="mb-0 list-unstyled">
                                            @foreach($archivos as $archivo)
                                                <li class="d-flex justify-content-between align-items-center py-1">
                                                    <div class="d-flex align-items-center justify-content-between w-100">
                                                        <span>{{ $archivo }}</span>
                                                        <form method="POST"
                                                              action="{{ route('admin.documento.archivo.eliminar') }}"
                                                              onsubmit="return confirm('¿Seguro que deseas eliminar este archivo?');"
                                                              class="ms-2">
                                                            @csrf
                                                            @method('DELETE')
                                                            <input type="hidden" name="ruta" value="{{ $documentoProveedor->ruta }}">
                                                            <input type="hidden" name="archivo" value="{{ $archivo }}">
                                                            <button type="submit"
                                                                    class="btn btn-outline-danger p-1"
                                                                    style="font-size: 0.75rem; line-height: 1; border-radius: 6px; width: 40px; height: 28px;"
                                                                    title="Eliminar archivo">
                                                                <i class="fas fa-trash fa-sm"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </li>
                                @endif
                            </ul>
                        </div>

                        @if($documentoProveedor->estado == 'cargado' && $documentoProveedor->ruta)
                            <!-- Botón de Descarga -->
                            <div class="col-lg-4 col-12 d-flex align-items-center justify-content-center">
                                <a class="btn btn-outline-primary w-100 w-lg-auto text-center text-sm"
                                   href="{{ route('admin.documento.descargar', [$documentoProveedor->id, $unicaVez]) }}"
                                   style="height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 10px; font-size: 18px;">
                                    <i class="fa fa-download me-2"></i> Descargar Documento(s)
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
                                    action="{{ route('admin.documento.guardar', [$documentoProveedor->id, $unicaVez]) }}"
                                    class="form-control dropzone"
                                    enctype="multipart/form-data"
                                    id="documento"
                                    method="POST">
                                    @csrf
                                </form>

                                <!-- Botón de envío -->
                                <button id="uploadButton" class="btn btn-outline-primary mt-3 float-end">
                                    <i class="fa fa-upload me-2"></i> Subir archivo(s)
                                </button>
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

        let documento = new Dropzone("#documento", {
            url: "{{ route('admin.documento.guardar', [$documentoProveedor->id, $unicaVez]) }}",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            acceptedFiles: ".pdf,.xlsx,.xls,.sue",
            addRemoveLinks: true,
            dictRemoveFile: "Eliminar",
            autoProcessQueue: false,
            uploadMultiple: false,
            parallelUploads: 1,
            maxFiles: 1000,
            maxFilesize: 4096,
            timeout: 0,
            dictDefaultMessage: "Clic para elegir los archivos a cargar",
            dictInvalidFileType: "Solo se permiten archivos PDF, XLSX, XLS, y SUE",
            dictMaxFilesExceeded: "Solo puedes subir 1000 archivos",
        });

        document.getElementById("uploadButton").addEventListener("click", function () {
            if (documento.files.length === 0) {
                alert("Por favor, sube un archivo antes de enviar.");
            } else {
                documento.processQueue();
            }
        });

        documento.on("success", function(file) {
            documento.removeFile(file);

            if (documento.getQueuedFiles().length > 0) {
                documento.processQueue();
            } else {
                alert('Los archivos han sido cargados con éxito.');
                window.location.reload();
            }
        });

        documento.on("error", function (file, response) {
            alert("Hubo un error al subir el archivo: " + response);
            documento.removeFile(file);
        });

    </script>
@endpush
