@extends('layouts.app')

@section('content')
    <div class="container py-5 text-center">
        <h3 class="mb-4">Generando tu archivo...</h3>
        <p class="text-muted">Estamos comprimiendo los documentos. Esta página se actualizará automáticamente cuando el archivo esté listo.</p>

        <div class="spinner-border text-primary my-4" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>

        <div id="estado" class="mt-4 text-secondary">Esperando finalización del proceso...</div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const rutaCheck = @json(route('admin.documentos.zip.progreso', ['nombre' => $nombre]));
            const rutaDescarga = @json(route('admin.documentos.zip.descargar', ['nombre' => $nombre]));

            const checkZip = () => {
                fetch(rutaCheck)
                    .then(res => res.json())
                    .then(data => {
                        if (data.listo) {
                            document.getElementById('estado').textContent = '¡Archivo listo! Iniciando descarga...';
                            clearInterval(interval);

                            const a = document.createElement('a');
                            a.href = rutaDescarga;
                            a.download = '';
                            document.body.appendChild(a);
                            a.click();
                            a.remove();

                            setTimeout(() => {
                                document.getElementById('estado').innerHTML = `
                                <strong>Descarga iniciada.</strong><br>
                                <a href="/" class="btn btn-sm btn-outline-primary mt-3">Volver al inicio</a>
                            `;
                            }, 3000);
                        }
                    });
            }

            const interval = setInterval(checkZip, 3000);
        });
    </script>
@endpush
