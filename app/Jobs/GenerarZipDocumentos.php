<?php

namespace App\Jobs;

use App\Helpers\Util;
use App\Models\Descarga;
use FilesystemIterator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class GenerarZipDocumentos implements ShouldQueue
{
    use Queueable;

    protected int $usuarioId;
    protected string $admin;
    protected ?string $cliente;
    protected int $tipo;
    protected ?int $anio;
    protected ?int $mes;
    protected ?string $tipoDocumento;
    protected ?string $proveedor;

    public function __construct(
        int $usuarioId,
        string $admin,
        ?string $cliente = null,
        int $tipo = 1,
        ?int $anio = null,
        ?int $mes = null,
        ?string $tipoDocumento = null,
        ?string $proveedor = null
    ) {
        $this->usuarioId = $usuarioId;
        $this->admin = $admin;
        $this->cliente = $cliente;
        $this->tipo = $tipo;
        $this->anio = $anio;
        $this->mes = $mes;
        $this->tipoDocumento = $tipoDocumento;
        $this->proveedor = $proveedor;
    }

    public function handle(): void
    {
        Log::info("[ZIP] Iniciando job para usuario ID: {$this->usuarioId}");

        $baseRuta = ["documentos", $this->admin];
        if ($this->cliente) $baseRuta[] = $this->cliente;
        if ($this->tipoDocumento) $baseRuta[] = $this->tipoDocumento;
        if ($this->proveedor) $baseRuta[] = $this->proveedor;

        $rutaBase = storage_path('app/public/' . implode('/', array_filter($baseRuta)));
        Log::info("[ZIP] Ruta base: $rutaBase");

        $rutaUnicaVezExtra = null;
        if ($this->tipoDocumento !== 'repse' && $this->cliente && $this->proveedor) {
            $rutaPosible = storage_path("app/public/documentos/{$this->admin}/{$this->cliente}/repse/{$this->proveedor}/única_vez");
            Log::info("[ZIP] Revisando ruta única vez: $rutaPosible");
            if (File::exists($rutaPosible)) {
                $rutaUnicaVezExtra = $rutaPosible;
                Log::info("[ZIP] Ruta única vez encontrada");
            }
        }

        $directorioZips = storage_path("app/zips");
        $nombreZip = implode('-', array_filter([
            $this->admin,
            $this->cliente,
            $this->tipoDocumento,
            $this->proveedor
        ]));

        $zipFinal = "$directorioZips/{$nombreZip}.zip";
        Log::info("[ZIP] Nombre del ZIP: $zipFinal");

        if (!File::exists($rutaBase)) {
            Log::warning("[ZIP] No se encontró la carpeta a comprimir: {$rutaBase}");
            Descarga::updateOrCreate(
                ['usuario_id' => $this->usuarioId, 'nombre' => $nombreZip, 'ruta' => $zipFinal],
                ['estado' => 'error']
            );
            return;
        }

        if (!File::exists($directorioZips)) {
            File::makeDirectory($directorioZips, 0755, true);
        }

        $descarga = Descarga::where('usuario_id', $this->usuarioId)
            ->where('nombre', $nombreZip)
            ->where('ruta', $zipFinal)
            ->latest()
            ->first();

        $zipModTime = File::exists($zipFinal) ? filemtime($zipFinal) : null;
        $archivos = collect(File::allFiles($rutaBase))
            ->merge($rutaUnicaVezExtra ? File::allFiles($rutaUnicaVezExtra) : []);

        $modificados = $archivos->filter(fn($f) => $zipModTime === null || $f->getMTime() > $zipModTime);
        Log::info("[ZIP] Archivos modificados: {$modificados->count()}");

        if ($descarga && $modificados->isEmpty() && File::exists($zipFinal)) {
            Log::info("[ZIP] No hay cambios. Se mantiene ZIP actual");
            $descarga->update([
                'estado' => 'completado',
                'tamaño' => File::size($zipFinal),
                'updated_at' => now(),
            ]);
            return;
        }

        File::delete($zipFinal);
        $zip = new \ZipArchive();
        Log::info("[ZIP] Creando nuevo ZIP...");

        if ($zip->open($zipFinal, \ZipArchive::CREATE | \ZipArchive::OVERWRITE)) {
            $mesNombre = $this->tipo === 3 && $this->mes
                ? Util::slugify(strtolower(Carbon::create()->month($this->mes)->locale('es')->translatedFormat('F')))
                : null;

            $iteradores = [
                new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($rutaBase, FilesystemIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::LEAVES_ONLY
                )
            ];

            if ($rutaUnicaVezExtra) {
                $iteradores[] = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($rutaUnicaVezExtra, FilesystemIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::LEAVES_ONLY
                );
            }

            foreach ($iteradores as $iterator) {
                foreach ($iterator as $file) {
                    if ($file->isDir()) continue;

                    $filePath = $file->getRealPath();
                    $relativePath = str_replace(storage_path('app/public/documentos/') . '/', '', $filePath);

                    if ($this->tipo === 2 && $this->anio && !str_contains($relativePath, "{$this->anio}/")) continue;
                    if ($this->tipo === 3 && $this->anio && $mesNombre && !str_contains($relativePath, "{$this->anio}/{$mesNombre}/")) continue;

                    $zip->addFile($filePath, $relativePath);
                }
            }

            $zip->close();
            Log::info("[ZIP] ZIP generado exitosamente");

            Descarga::updateOrCreate(
                ['usuario_id' => $this->usuarioId, 'nombre' => $nombreZip, 'ruta' => $zipFinal],
                ['estado' => 'completado', 'tamaño' => File::size($zipFinal)]
            );
        } else {
            Log::error("[ZIP] No se pudo abrir el archivo ZIP en: {$zipFinal}");
            Descarga::updateOrCreate(
                ['usuario_id' => $this->usuarioId, 'nombre' => $nombreZip, 'ruta' => $zipFinal],
                ['estado' => 'error']
            );
        }
    }
}
