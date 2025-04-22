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
        $this->usuarioId = $usuarioId;
        $this->admin = $admin;
        $this->cliente = $cliente;
        $this->tipo = $tipo;
        $this->anio = $anio;
        $this->mes = $mes;
        $this->tipoDocumento = $tipoDocumento;
        $this->proveedor = $proveedor;
        $this->incluirUnicaVez = $incluirUnicaVez;
    }

    public function handle(): void
    {
        Log::info("[ZIP] Iniciando job para usuario ID: {$this->usuarioId}");
        Log::info("Incluir única vez: " . ($this->incluirUnicaVez ? 'Sí' : 'No') . " - Valor: {$this->incluirUnicaVez}");

        $baseRuta = ["documentos", $this->admin];
        if ($this->cliente) $baseRuta[] = $this->cliente;
        if ($this->tipoDocumento) $baseRuta[] = $this->tipoDocumento;
        if ($this->proveedor) $baseRuta[] = $this->proveedor;

        $rutaBase = storage_path('app/public/' . implode('/', array_filter($baseRuta)));
        $relativaDesde = storage_path('app/public/documentos');

        $directorioZips = storage_path("app/zips");
        $mesNombre = $this->tipo === 3 && $this->mes
            ? Util::slugify(strtolower(Carbon::create()->month($this->mes)->locale('es')->translatedFormat('F')))
            : null;

        $hashComponentes = implode('|', array_filter([
            $this->cliente,
            $this->tipoDocumento,
            $this->proveedor,
            $this->tipo === 2 ? $this->anio : null,
            $this->tipo === 3 ? ($this->anio . '-' . $mesNombre) : null,
        ]));
        $hash = substr(sha1($hashComponentes), 0, 8);

        $nombreZip = implode('-', array_filter([
                $this->admin,
                $this->tipo === 2 && $this->anio ? $this->anio : null,
                $this->tipo === 3 && $this->anio && $mesNombre ? "{$this->anio}-{$mesNombre}" : null,
            ])) . '-' . $hash;

        $zipFinal = "$directorioZips/{$nombreZip}.zip";

        if (!File::exists($rutaBase)) {
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

        $archivosActuales = collect(File::allFiles($rutaBase))
            ->reject(fn($file) => !$this->incluirUnicaVez && str_contains($file->getRealPath(), '/única_vez'))
            ->mapWithKeys(function ($file) use ($relativaDesde) {
                $path = $file->getRealPath();
                $rel = substr($path, strlen($relativaDesde) + 1);
                return [$rel => $file->getMTime()];
            });

        $archivosZip = collect();
        if (File::exists($zipFinal)) {
            $zip = new ZipArchive();
            if ($zip->open($zipFinal) === true) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $stat = $zip->statIndex($i);
                    if (!$stat) continue;
                    $archivosZip[$stat['name']] = $stat['mtime'] ?? 0;
                }
                $zip->close();
            }
        }

        $archivosModificados = $archivosActuales->filter(function ($mtime, $path) use ($archivosZip) {
            return !isset($archivosZip[$path]) || $archivosZip[$path] < $mtime;
        });

        $archivosEliminados = $archivosZip->keys()->diff($archivosActuales->keys());

        if ($archivosModificados->isEmpty() && $archivosEliminados->isEmpty() && File::exists($zipFinal)) {
            Log::info("[ZIP] Sin cambios detectados, se mantiene ZIP actual");
            $descarga->update([
                'estado' => 'completado',
                'tamaño' => File::size($zipFinal),
                'updated_at' => now(),
            ]);
            return;
        }

        File::delete($zipFinal);
        $zip = new ZipArchive();

        if ($zip->open($zipFinal, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
            $archivosAgregados = 0;
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($rutaBase, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($iterator as $file) {
                if ($file->isDir()) continue;
                if (!$this->incluirUnicaVez && str_contains($file->getRealPath(), '/única_vez')) continue;

                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($relativaDesde) + 1);

                if ($this->tipo === 2 && $this->anio && !str_contains($relativePath, "{$this->anio}/")) continue;
                if ($this->tipo === 3 && $this->anio && $mesNombre && !str_contains($relativePath, "{$this->anio}/{$mesNombre}/")) continue;

                $zip->addFile($filePath, $relativePath);
                $archivosAgregados++;
            }

            if ($archivosAgregados === 0) {
                $mensaje = "No hay archivos disponibles para los parámetros seleccionados.";
                $archivoTemporal = storage_path("app/temp-mensaje.txt");
                File::put($archivoTemporal, $mensaje);
                $zip->addFile($archivoTemporal, "{$this->admin}/{$this->cliente}/mensaje.txt");
            }

            $zip->close();

            if (!File::exists($zipFinal)) {
                Descarga::updateOrCreate(
                    ['usuario_id' => $this->usuarioId, 'nombre' => $nombreZip, 'ruta' => $zipFinal],
                    ['estado' => 'error']
                );
                return;
            }

            Descarga::updateOrCreate(
                ['usuario_id' => $this->usuarioId, 'nombre' => $nombreZip, 'ruta' => $zipFinal],
                ['estado' => 'completado', 'tamaño' => File::size($zipFinal)]
            );
        } else {
            Descarga::updateOrCreate(
                ['usuario_id' => $this->usuarioId, 'nombre' => $nombreZip, 'ruta' => $zipFinal],
                ['estado' => 'error']
            );
        }
    }
}
