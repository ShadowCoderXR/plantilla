<?php

namespace App\Jobs;

use App\Helpers\Util;
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

    protected string $admin;
    protected ?string $cliente;
    protected int $tipo;
    protected ?int $anio;
    protected ?int $mes;
    protected ?string $tipoDocumento;
    protected ?string $proveedor;

    /**
     * Create a new job instance.
     */
    public function __construct(
        string $admin,
        ?string $cliente = null,
        int $tipo = 1,
        ?int $anio = null,
        ?int $mes = null,
        ?string $tipoDocumento = null,
        ?string $proveedor = null
    ) {
        $this->admin          = $admin;
        $this->cliente        = $cliente;
        $this->tipo           = $tipo;
        $this->anio           = $anio;
        $this->mes            = $mes;
        $this->tipoDocumento  = $tipoDocumento;
        $this->proveedor      = $proveedor;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $partes = ["documentos", $this->admin];
        if ($this->cliente)         $partes[] = $this->cliente;
        if ($this->tipoDocumento)   $partes[] = $this->tipoDocumento;
        if ($this->proveedor)       $partes[] = $this->proveedor;

        $rutaBase = storage_path('app/public/' . implode('/', $partes));
        $directorioZips = storage_path("app/zips");

        $nombreZip = implode('-', array_filter([
            $this->admin,
            $this->cliente,
            $this->tipoDocumento,
            $this->proveedor
        ]));

        $zipTemp  = "{$directorioZips}/{$nombreZip}.en_progreso.zip";
        $zipFinal = "{$directorioZips}/{$nombreZip}.zip";

        if (!File::exists($rutaBase)) {
            Log::warning("No se encontró la carpeta a comprimir: {$rutaBase}");
            return;
        }

        if (!File::exists($directorioZips)) {
            File::makeDirectory($directorioZips, 0755, true);
        }

        File::delete($zipTemp);
        File::delete($zipFinal);

        $zip = new \ZipArchive();

        if ($zip->open($zipTemp, \ZipArchive::CREATE | \ZipArchive::OVERWRITE)) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($rutaBase, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            $mesNombre = null;
            if ($this->tipo === 3 && $this->mes) {
                $mesNombre = Util::slugify(
                    strtolower(Carbon::create()->month($this->mes)->locale('es')->translatedFormat('F'))
                );
            }

            foreach ($files as $file) {
                if (!$file->isDir()) {
                    $filePath     = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($rutaBase) + 1);

                    if (str_contains($relativePath, 'única_vez')) {
                        $zip->addFile($filePath, $relativePath);
                        continue;
                    }

                    if ($this->tipo === 2 && $this->anio) {
                        if (!str_contains($relativePath, "/{$this->anio}/")) continue;
                    }

                    if ($this->tipo === 3 && $this->anio && $mesNombre) {
                        if (!str_contains($relativePath, "/{$this->anio}/{$mesNombre}/")) continue;
                    }

                    $zip->addFile($filePath, $relativePath);
                }
            }

            $zip->close();
            rename($zipTemp, $zipFinal);
        } else {
            Log::error("No se pudo abrir el archivo ZIP en: {$zipTemp}");
        }
    }
}
