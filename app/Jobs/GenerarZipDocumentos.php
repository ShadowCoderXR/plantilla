<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class GenerarZipDocumentos implements ShouldQueue
{
    use Queueable;

    protected $nombreAdmin;

    /**
     * Create a new job instance.
     */
    public function __construct(string $nombreAdmin)
    {
        $this->nombreAdmin = $nombreAdmin;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $ruta = storage_path("app/public/documentos/{$this->nombreAdmin}");
        $directorioZips = storage_path("app/zips");
        $archivoProgreso = "{$directorioZips}/{$this->nombreAdmin}.en_progreso.zip";
        $archivoFinal = "{$directorioZips}/{$this->nombreAdmin}.zip";

        if (!File::exists($ruta)) {
            Log::warning("No se encontró la carpeta a comprimir: {$ruta}");
            return;
        }

        if (!File::exists($directorioZips)) {
            File::makeDirectory($directorioZips, 0755, true);
        }

        File::delete($archivoProgreso);
        File::delete($archivoFinal);

        $zip = new \ZipArchive();
        if ($zip->open($archivoProgreso, \ZipArchive::CREATE | \ZipArchive::OVERWRITE)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($ruta),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($ruta) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }

            $zip->close();
            rename($archivoProgreso, $archivoFinal);
        } else {
            Log::error("No se pudo abrir el archivo ZIP en: {$archivoProgreso}");
        }
    }
}
