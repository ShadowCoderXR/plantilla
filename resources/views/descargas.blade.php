@extends('layouts.app')

@section('breadcrumb' , 'Descargas')
@section('title' , 'Progreso de Descargas')
@section('back-button' , true)

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header pb-0">
                    <h5 class="mb-0">Historial de Descargas</h5>
                    <p class="text-sm mb-0 text-muted">Progreso de todas las descargas solicitadas</p>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-items-center datatable" id="tabla-descargas">
                            <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th>Tamaño</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($descargas as $descarga)
                                <tr data-nombre="{{ $descarga->nombre }}">
                                    <td class="text-sm">{{ $descarga->nombre }}</td>
                                    <td class="text-sm text-wrap" style="max-width: 250px; word-break: break-word;">
                                        {{ $descarga->descripcion ?? '—' }}
                                    </td>
                                    <td>
                                        <span class="badge bg-gradient-{{
                                            $descarga->estado === 'completado' ? 'success' :
                                            ($descarga->estado === 'en_proceso' ? 'warning' : 'danger')
                                        }}">
                                            {{ ucfirst(str_replace('_', ' ', $descarga->estado)) }}
                                        </span>
                                    </td>
                                    <td class="text-sm">
                                        @php
                                            $size = $descarga->tamaño ?? 0;
                                        @endphp

                                        @if($size > 0)
                                            @if($size < 1024)
                                                {{ $size }} B
                                            @elseif($size < 1024 * 1024)
                                                {{ number_format($size / 1024, 2) }} KB
                                            @else
                                                {{ number_format($size / (1024 * 1024), 2) }} MB
                                            @endif
                                        @else
                                            &mdash;
                                        @endif
                                    </td>
                                    <td class="text-sm">{{ $descarga->updated_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($descarga->estado === 'completado')
                                            <a href="{{ route('admin.documentos.zip.descargar', $descarga->nombre) }}" class="btn btn-sm btn-outline-success">
                                                <i class="fa fa-download me-1"></i> Descargar
                                            </a>
                                        @else
                                            <span class="text-muted text-xs">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            new simpleDatatables.DataTable("#tabla-descargas", {
                searchable: false,
                sortable: false,
                fixedHeight: true
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let intervalId = null;

            function actualizarProgreso() {
                const filas = document.querySelectorAll('#tabla-descargas tbody tr');
                let hayPendientes = false;
                let debeRecargar = false;

                const promesas = Array.from(filas).map(fila => {
                    const nombre    = fila.dataset.nombre;
                    const badge     = fila.querySelector('td:nth-child(3) span');
                    const estadoTxt = badge?.textContent.trim().toLowerCase() ?? '';

                    if (estadoTxt === 'en proceso' || estadoTxt === 'en_proceso') {
                        hayPendientes = true;

                        return fetch(`/admin/documentos/zip-progreso/${encodeURIComponent(nombre)}`)
                            .then(res => res.json())
                            .then(data => {
                                if (!data || data.estado === 'no_encontrado') return;

                                if (data.estado !== 'en_proceso') {
                                    debeRecargar = true;
                                }
                            })
                            .catch(console.error);
                    }

                    return Promise.resolve();
                });

                Promise.all(promesas).then(() => {
                    if (debeRecargar) {
                        location.reload();
                    }

                    if (!hayPendientes && intervalId) {
                        clearInterval(intervalId);
                        intervalId = null;
                    }
                });
            }

            intervalId = setInterval(actualizarProgreso, 3000);
        });
    </script>
@endpush
