<?php

namespace App\Jobs;

use App\Helpers\Util;
use App\Models\Descarga;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
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

        $segments      = array_filter(['documentos', $this->admin, $this->cliente, $this->proveedor, $this->tipoDocumento]);
        $rutaBase      = storage_path('app/public/' . implode('/', $segments));
        $relativaDesde = storage_path('app/public/documentos');
        $directorioZips= storage_path('app/zips');

        $mesNombre = null;
        if ($this->tipo === 3 && $this->mes) {
            $mesNombre = Util::slugify(
                strtolower(
                    Carbon::create()->month($this->mes)->locale('es')->translatedFormat('F')
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

        $nameParts = array_filter([
            $this->admin,
            $this->tipo === 2 ? (string)$this->anio : null,
            $this->tipo === 3 ? "{$this->anio}-{$mesNombre}" : null,
        ]);
        $nombreSinExt = implode('-', $nameParts) . '-' . $hash;
        $zipFilename = "{$nombreSinExt}.zip";
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

        if (! File::exists($directorioZips)) {
            File::makeDirectory($directorioZips, 0755, true);
        }

        $todos = collect();
        if ($this->incluirUnicaVez) {
            $u1 = "{$rutaBase}/única_vez";
            if (File::exists($u1)) {
                $todos = $todos->merge(File::allFiles($u1));
            }
            if ($this->cliente && $this->proveedor && $this->tipoDocumento !== 'repse') {
                $u2 = storage_path("app/public/documentos/{$this->admin}/{$this->cliente}/{$this->proveedor}/repse/única_vez");
                if (File::exists($u2)) {
                    $todos = $todos->merge(File::allFiles($u2));
                }
            }
        }
        if (File::exists($rutaBase)) {
            $todos = $todos->merge(File::allFiles($rutaBase));
        }

        $coleccion = $todos;

        Log::info("[ZIP] Total archivos recolectados: {$coleccion->count()}");

        $archivosActuales = $coleccion
            ->mapWithKeys(fn($file) => [ substr($file->getRealPath(), strlen($relativaDesde) + 1) => $file->getMTime() ])
            ->filter(function($mtime, $rel) use ($mesNombre) {
                if ($this->incluirUnicaVez && str_contains($rel, 'única_vez')) {
                    return true;
                }
                if (! $this->incluirUnicaVez && str_contains($rel, 'única_vez')) {
                    return false;
                }
                if ($this->tipo === 2 && $this->anio && ! str_contains($rel, "{$this->anio}\\")) {
                    return false;
                }
                if ($this->tipo === 3 && $this->anio && $mesNombre && ! str_contains($rel, "{$this->anio}\\{$mesNombre}\\")) {
                    return false;
                }
                return true;
            });
        Log::info("[ZIP] Archivos tras filtro: {$archivosActuales->count()}");

        if ($archivosActuales->isEmpty()) {
            Log::info("[ZIP] No hay documentos; marcando como sin_documentos y saliendo");
            $descarga->update([
                'estado'     => 'sin_documentos',
                'tamaño'     => 0,
                'updated_at' => now(),
            ]);
            return;
        }

        $zipOld = collect();
        if (File::exists($zipFinal)) {
            $zipReader = new ZipArchive();
            if ($zipReader->open($zipFinal) === true) {
                for ($i = 0; $i < $zipReader->numFiles; $i++) {
                    $st = $zipReader->statIndex($i);
                    if ($st) {
                        $zipOld[$st['name']] = $st['mtime'] ?? 0;
                    }
                }
                $zipReader->close();
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
        $zipWriter = new ZipArchive();
        if ($zipWriter->open($zipFinal, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
            foreach ($archivosActuales as $rel => $_) {
                $zipWriter->addFile("{$relativaDesde}/{$rel}", $rel);
            }
            $zipWriter->close();
        }

        $descarga->update([
            'estado' => File::exists($zipFinal) ? 'completado' : 'error',
            'tamaño' => File::size($zipFinal) ?: null,
        ]);
    }
}
