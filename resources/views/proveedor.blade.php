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
                            <img alt="vp360" class="border-radius-lg shadow-sm img-fluid" src="{{ asset('img/logos/google.jpg') }}" style="width: 100%; height: 100%; object-fit: contain;">
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
                                    <span class="fw-bold">Teléfono:</span> {{ $proveedor->telefono }}
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="fas fa-envelope me-2 text-danger"></i>
                                    <span class="fw-bold">Correo:</span> {{ $proveedor->correo }}
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="fas fa-calendar-alt me-2 text-success"></i>
                                    <span class="fw-bold">Fecha de Registro:</span> {{ $proveedor->created_at->format('d/m/Y') }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Descripción Adicional -->
        <div class="col-md-6 d-flex mb-4">
            <div class="card p-3 w-100 d-flex flex-column">
                <div class="card-body flex-grow-1">
                    <h5 class="mb-3">Descripción Adicional</h5>
                    <p class="text-muted">
                        {{ $proveedor->descripcion_adicional }}
                    </p>
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
                            <li class="list-inline-item me-3 text-bold"> Simboligía: </li>
                            <li class="list-inline-item me-3">
                                <i class="fa fa-check text-success"></i> Presente
                            </li>
                            <li class="list-inline-item me-3">
                                <i class="fa fa-upload text-warning"></i> Cargando
                            </li>
                            <li class="list-inline-item">
                                <i class="fa fa-times text-danger"></i> Faltante
                            </li>
                        </ul>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="me-2 text-sm">Año:</span>
                        <select class="form-select form-select-lg yearSelector" style="width: auto; min-width: 150px;">
                            <option value="2025">2025</option>
                            <option value="2024">2024</option>
                            <option value="2023">2023</option>
                            <option value="2022">2022</option>
                            <option value="2021">2021</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-items-center" id="datatable-search">
                            <thead class="thead-light">
                                <tr>
                                    <th>Documento</th>
                                    @foreach(range(1, 12) as $mes)
                                        <th>{{ DateTime::createFromFormat('!m', $mes)->format('M') }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($documentos as $tipoDocumentoId => $meses)
                                <tr>
                                    <td>{{ $meses[array_key_first($meses)]['nombre'] }}</td>
                                    @foreach(range(1, 12) as $mes)
                                        <td>
                                            @php
                                                $estado = $meses[$mes]['estado'] ?? 4; // 4 = No requerido
                                            @endphp
                                            @if($estado == 1)
                                                ✅ <!-- Documento presente -->
                                            @elseif($estado == 2)
                                                ⚠️ <!-- Documento en carga -->
                                            @elseif($estado == 3)
                                                ❌ <!-- Documento faltante -->
                                            @endif
                                        </td>
                                    @endforeach
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
    <!-- Script para llenar la tabla de documentos con datos aleatorios (Eliminar al llenar con datos de base de datos) -->
{{--    <script>--}}
{{--        document.addEventListener("DOMContentLoaded", function () {--}}
{{--            const empresa = {--}}
{{--                documentos: [--}}
{{--                    "Comprobante de aplicación de pago de cuotas",--}}
{{--                    "Comprobante de pago de RCV (Retro, Cesantía y Vejez) o INFONAVIT",--}}
{{--                    "CDFI Nómina (XML)",--}}
{{--                    "Acuse de recibo de la declaración de IVA",--}}
{{--                    "Acuse de recibo de la declaración de ISR",--}}
{{--                    "Comprobante Bancario de Aplicación de la declaración de IVA",--}}
{{--                    "Comprobante Bancario de Aplicación de la declaración de ISR",--}}
{{--                    "SISUB",--}}
{{--                    "ICSOE",--}}
{{--                    "SUA",--}}
{{--                    "Lista de asistencia de personal que ejecutó el servicio",--}}
{{--                ]--}}
{{--            };--}}

{{--            function generarMatrizDocumentos() {--}}
{{--                let matriz = [];--}}
{{--                for (let i = 0; i < 12; i++) {--}}
{{--                    let randomValue = Math.random();--}}
{{--                    let icono;--}}

{{--                    if (randomValue > 0.60) {--}}
{{--                        icono = `<a href="{{ route('admin.documento', 1) }}"><i class="fa fa-check text-success"></i></a>`;--}}
{{--                    } else if (randomValue > 0.30) {--}}
{{--                        icono = `<a href="{{ route('admin.documento', 3) }}"><i class="fa fa-times text-danger"></i></a>`;--}}
{{--                    } else if (randomValue > 0.20) {--}}
{{--                        icono = `<a href="{{ route('admin.documento', 2) }}"><i class="fa fa-upload text-warning"></i></a>`;--}}
{{--                    } else {--}}
{{--                        icono = `<span>&nbsp;</span>`;--}}
{{--                    }--}}

{{--                    matriz.push(icono);--}}
{{--                }--}}
{{--                return matriz;--}}
{{--            }--}}

{{--            const tbody = document.querySelector("#datatable-search tbody");--}}

{{--            empresa.documentos.forEach(doc => {--}}
{{--                const matriz = generarMatrizDocumentos();--}}
{{--                const fila = document.createElement("tr");--}}

{{--                fila.innerHTML = `--}}
{{--                <td class="text-sm font-weight-normal">${doc}</td>--}}
{{--                ${matriz.map(status => `<td class="text-sm font-weight-normal text-center">${status}</td>`).join("")}--}}
{{--              `;--}}

{{--                tbody.appendChild(fila);--}}
{{--            });--}}

{{--            const dataTableSearch = new simpleDatatables.DataTable("#datatable-search", {--}}
{{--                searchable: false,--}}
{{--                paging: false,--}}
{{--                sortable: false,--}}
{{--            });--}}
{{--        });--}}
{{--    </script>--}}

    <!-- Script para inicializar la tabla de documentos (descomentar al llenar con datos de base de datos) -->
    <script>
        const dataTableSearch = new simpleDatatables.DataTable("#datatable-search", {
            searchable: true,
            paging: true,
            sortable: true,
        });
    </script>
@endpush


