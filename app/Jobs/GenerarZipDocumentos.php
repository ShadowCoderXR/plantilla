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
use ZipArchive;

class GenerarZipDocumentos implements ShouldQueue
{
    use Queueable;
    public $timeout = 0;

    protected int $usuarioId;
    protected string $admin;
    protected ?string $cliente;
    protected int $tipo;
    protected ?int $anio;
    protected ?int $mes;
    protected ?string $tipoDocumento;
    protected ?string $proveedor;
    protected bool $incluirUnicaVez;

    public function __construct(
        int $usuarioId,
        string $admin,
        ?string $cliente = null,
        int $tipo = 1,
        ?int $anio = null,
        ?int $mes = null,
        ?string $tipoDocumento = null,
        ?string $proveedor = null,
        bool $incluirUnicaVez = false
    ) {
        $this->usuarioId       = $usuarioId;
        $this->admin           = $admin;
        $this->cliente         = $cliente;
        $this->tipo            = $tipo;
        $this->anio            = $anio;
        $this->mes             = $mes;
        $this->tipoDocumento   = $tipoDocumento;
        $this->proveedor       = $proveedor;
        $this->incluirUnicaVez = $incluirUnicaVez;
    }

    public function handle(): void
    {
        Log::info("[ZIP] Iniciando job para usuario ID: {$this->usuarioId}");

        $base = array_filter(['documentos', $this->admin, $this->cliente, $this->tipoDocumento, $this->proveedor]);
        $rutaBase      = storage_path('app/public/' . implode('/', $base));
        $relativaDesde = storage_path('app/public/documentos');
        $directorioZips= storage_path('app/zips');

        $mesNombre = null;
        if ($this->tipo === 3 && $this->mes) {
            $mesNombre = Util::slugify(
                strtolower(
                    Carbon::create()
                        ->month($this->mes)
                        ->locale('es')
                        ->translatedFormat('F')
                )
            );
        }

        $hashParts = array_filter([
            $this->cliente,
            $this->tipoDocumento,
            $this->proveedor,
            $this->tipo === 2 ? $this->anio : null,
            $this->tipo === 3 ? "{$this->anio}-{$mesNombre}" : null,
            $this->incluirUnicaVez ? 'unica_vez' : null,
        ]);
        $hash = substr(sha1(implode('|', $hashParts)), 0, 8);
        Log::info("[ZIP] Hash job: {$hash}");

        $nameParts    = array_filter([
            $this->admin,
            $this->tipo === 2 ? (string)$this->anio : null,
            $this->tipo === 3 ? "{$this->anio}-{$mesNombre}" : null,
        ]);
        $nombreSinExt = implode('-', $nameParts) . '-' . $hash;

        $zipFilename = $nombreSinExt . '.zip';
        $zipFinal    = "{$directorioZips}/{$zipFilename}";

        $descarga = Descarga::where('usuario_id', $this->usuarioId)
            ->where('nombre', $nombreSinExt)
            ->where('ruta',   $zipFinal)
            ->latest()
            ->first();

        if (! $descarga) {
            Log::error("[ZIP] No se encontró Descarga para {$nombreSinExt}");
            return;
        }

        if (! File::exists($rutaBase)) {
            $descarga->update(['estado' => 'error']);
            return;
        }

        if (! File::exists($directorioZips)) {
            File::makeDirectory($directorioZips, 0755, true);
        }

        $coleccion = collect();

        if ($this->incluirUnicaVez) {
            $u1 = "{$rutaBase}/única_vez";
            if (File::exists($u1)) {
                $coleccion = $coleccion->merge(File::allFiles($u1));
            }
            if ($this->cliente && $this->proveedor && $this->tipoDocumento !== 'repse') {
                $u2 = storage_path("app/public/documentos/{$this->admin}/{$this->cliente}/repse/{$this->proveedor}/única_vez");
                if (File::exists($u2)) {
                    $coleccion = $coleccion->merge(File::allFiles($u2));
                }
            }
        }

        $coleccion = $coleccion->merge(File::allFiles($rutaBase));
        Log::info("[ZIP] Total archivos recolectados: {$coleccion->count()}");

        $archivosActuales = $coleccion
            ->mapWithKeys(function($file) use ($relativaDesde) {
                $rel = substr($file->getRealPath(), strlen($relativaDesde) + 1);
                return [$rel => $file->getMTime()];
            })
            ->filter(function($mtime, $rel) use ($mesNombre) {
                if ($this->incluirUnicaVez && str_contains($rel, 'única_vez')) {
                    return true;
                }
                if (! $this->incluirUnicaVez && str_contains($rel, 'única_vez')) {
                    return false;
                }
                if ($this->tipo === 2 && $this->anio && ! str_contains($rel, "{$this->anio}/")) {
                    return false;
                }
                if ($this->tipo === 3 && $this->anio && $mesNombre && ! str_contains($rel, "{$this->anio}/{$mesNombre}/")) {
                    return false;
                }
                return true;
            });
        Log::info("[ZIP] Archivos tras filtro: {$archivosActuales->count()}");

        $zipOld = collect();
        if (File::exists($zipFinal)) {
            $zip = new ZipArchive();
            if ($zip->open($zipFinal) === true) {
                for ($i=0; $i<$zip->numFiles; $i++) {
                    $st = $zip->statIndex($i);
                    if ($st) $zipOld[$st['name']] = $st['mtime'] ?? 0;
                }
                $zip->close();
            }
        }
        $modificados = $archivosActuales->filter(fn($mt,$rp) => ! isset($zipOld[$rp]) || $zipOld[$rp] < $mt);
        $eliminados  = $zipOld->keys()->diff($archivosActuales->keys());
        Log::info("[ZIP] Modificados: {$modificados->count()}, Eliminados: {$eliminados->count()}");

        if (File::exists($zipFinal) && $modificados->isEmpty() && $eliminados->isEmpty()) {
            Log::info("[ZIP] Sin cambios, manteniendo zip existente");
            $descarga->update([
                'estado'     => 'completado',
                'tamaño'     => File::size($zipFinal),
                'updated_at' => now(),
            ]);
            return;
        }

        File::delete($zipFinal);
        $zip = new ZipArchive();
        if ($zip->open($zipFinal, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
            foreach ($archivosActuales as $rel => $_) {
                $zip->addFile("{$relativaDesde}/{$rel}", $rel);
            }
            if ($archivosActuales->isEmpty()) {
                $tmp = storage_path('app/temp-mensaje.txt');
                File::put($tmp, 'No hay archivos disponibles para los parámetros seleccionados.');
                $zip->addFile($tmp, "{$this->admin}/mensaje.txt");
            }
            $zip->close();
        }

        $descarga->update([
            'estado' => File::exists($zipFinal) ? 'completado' : 'error',
            'tamaño' => File::size($zipFinal) ?: null,
        ]);
    }
}
