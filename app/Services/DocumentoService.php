<?php

namespace App\Services;

use App\Helpers\Util;
use App\Models\DocumentoProveedor;
use App\Models\DocumentoProveedorUnicaVez;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class DocumentoService
{
    public static function generarResultadoMensual(
        Collection $documentosAgrupados,
        Collection $matriz,
        Collection $documentosProveedor,
        object $clienteProveedor,
        int $anio
    ): array {
        $mesesNombre = DocumentoProveedor::MESES;
        $resultado = [];

        foreach ($documentosAgrupados as $grupo => $docs) {
            foreach ($docs as $doc) {
                $fila = [
                    'id_documento' => $doc->id,
                    'documento'    => $doc->nombre,
                    'informacion'  => $doc->informacion,
                    'grupo'        => $grupo,
                    'meses'        => [],
                ];

                for ($mes = 1; $mes <= 12; $mes++) {
                    $mesNombre = $mesesNombre[$mes];

                    $esObligatorio = $matriz[$doc->id]->firstWhere('mes', $mesNombre)?->obligatorio ?? false;
                    $registro = $documentosProveedor[$doc->id]->firstWhere('mes', $mesNombre);

                    if ($registro) {
                        $fila['meses'][$mes] = [
                            'estado' => $registro->estado,
                            'id'     => $registro->id,
                        ];
                    } elseif ($esObligatorio) {
                        $estado = DocumentoProveedor::estadoSegunFecha($anio, $mes);

                        $nuevo = DocumentoProveedor::create([
                            'cliente_proveedor_id' => $clienteProveedor->id,
                            'documento_id'         => $doc->id,
                            'anio'                 => $anio,
                            'mes'                  => $mesNombre,
                            'estado'               => $estado,
                        ]);

                        $fila['meses'][$mes] = [
                            'estado' => $estado,
                            'id'     => $nuevo->id,
                        ];
                    } else {
                        $fila['meses'][$mes] = [
                            'estado' => 'no_requerido',
                            'id'     => null,
                        ];
                    }
                }

                $resultado[] = $fila;
            }
        }

        return $resultado;
    }

    public static function obtenerDocumentoProveedor(int $id, ?string $unicaVez = null): object
    {
        return match ($unicaVez) {
            'uv'    => DocumentoProveedorUnicaVez::with('documento')->findOrFail($id),
            null    => DocumentoProveedor::with('documento')->findOrFail($id),
            default => throw new \InvalidArgumentException('Parámetro inválido'),
        };
    }

    public static function obtenerArchivos(string $rutaRelativa): array
    {
        $folderPath = storage_path("app/public/{$rutaRelativa}");

        if (!File::exists($folderPath)) {
            return [];
        }

        return collect(File::files($folderPath))->map(fn($file) => $file->getFilename())->toArray();
    }

    public static function actualizarEstadoSiNoHayArchivos(object $documento, array $archivos, ?string $unicaVez = null): void
    {
        if (count($archivos) > 0) {
            return;
        }

        if ($unicaVez === 'uv') {
            $documento->estado = DocumentoProveedor::ESTATUS_POR_CARGAR;
        } else {
            $estado = DocumentoProveedor::estadoSegunFecha((int) $documento->anio, (int) $documento->mes);
            $documento->estado = $estado;
        }

        $documento->fecha_carga = null;
        $documento->save();
    }

    public static function guardarDocumento(Request $request, int $id, ?string $unicaVez = null): array
    {
        $request->validate([
            'file' => 'required|mimes:pdf,xlsx,xls,sue|max:4096',
        ]);

        $archivo = $request->file('file');
        if (!$archivo) {
            return [
                'error' => true,
                'status' => 400,
                'message' => 'No se recibió ningún archivo.'
            ];
        }

        $documento = match ($unicaVez) {
            'uv'    => DocumentoProveedorUnicaVez::with([
                'clienteProveedor.cliente.administradores',
                'clienteProveedor.proveedor',
                'documento.grupo.tipoDocumento'
            ])->findOrFail($id),
            null    => DocumentoProveedor::with([
                'clienteProveedor.cliente.administradores',
                'clienteProveedor.proveedor',
                'documento.grupo.tipoDocumento'
            ])->findOrFail($id),
            default => throw new \InvalidArgumentException('Parámetro inválido')
        };

        [$ruta, $nombreFinal] = self::generarRutaYNombre($documento, $archivo);

        if ($documento->ruta && Storage::disk('public')->exists($documento->ruta)) {
            Storage::disk('public')->deleteDirectory($documento->ruta);
        }

        $rutaArchivo = $archivo->storeAs($ruta, $nombreFinal, 'public');
        $posicionUltimoSlash = strrpos($rutaArchivo, '/');
        $rutaArchivo = substr($rutaArchivo, 0, $posicionUltimoSlash);

        $documento->ruta = $rutaArchivo;
        $documento->estado = DocumentoProveedor::ESTATUS_CARGADO;
        $documento->fecha_carga = now();
        $documento->save();

        return [
            'error' => false,
            'status' => 200,
            'message' => 'Archivo subido correctamente.',
            'ruta' => $rutaArchivo,
        ];
    }

    private static function generarRutaYNombre($documento, UploadedFile $archivo): array
    {
        $admin     = Util::slugify($documento->clienteProveedor->cliente->administradores->first()->nombre ?? 'administrador');
        $cliente   = Util::slugify($documento->clienteProveedor->cliente->nombre ?? 'cliente');
        $proveedor = Util::slugify($documento->clienteProveedor->proveedor->nombre ?? 'proveedor');
        $tipoSlug  = Util::slugify($documento->documento->grupo->tipoDocumento->nombre ?? 'tipo');
        $grupoSlug = Util::slugify($documento->documento->grupo->nombre ?? 'grupo');
        $anio      = $documento->anio;
        $mes       = $documento->mes;

        if ($tipoSlug !== $grupoSlug) {
            $tipoSlug = $grupoSlug;
        }

        $nombreDocumento = $documento->documento->nombre ?? 'documento';

        $padre = str_contains($nombreDocumento, '- ISR') ? 'declaraciones_mensuales_retenciones_isr'
            : (str_contains($nombreDocumento, '- IVA') ? 'declaraciones_mensuales_retenciones_iva' : null);

        $subcarpeta = Util::slugify($nombreDocumento);
        $nombreNormalizado = $padre ? "{$padre}/{$subcarpeta}" : $subcarpeta;
        $nombreFinal = $archivo->getClientOriginalName();

        $ruta = $documento->documento->informacion === 'Documento única vez'
            ? "documentos/{$admin}/{$cliente}/{$tipoSlug}/{$proveedor}/única_vez/{$nombreNormalizado}"
            : "documentos/{$admin}/{$cliente}/{$tipoSlug}/{$proveedor}/{$anio}/{$mes}/{$nombreNormalizado}";

        return [$ruta, $nombreFinal];
    }

    public static function descargarYComprimir(int $id, ?string $unicaVez = null)
    {
        $documento = match ($unicaVez) {
            'uv'    => DocumentoProveedorUnicaVez::findOrFail($id),
            null    => DocumentoProveedor::findOrFail($id),
            default => throw new \InvalidArgumentException('Parámetro inválido'),
        };

        $folderPath = storage_path("app/public/{$documento->ruta}");

        if (!File::exists($folderPath)) {
            return response()->json(['error' => 'La carpeta no existe.'], 404);
        }

        $files = File::allFiles($folderPath);
        if (count($files) === 0) {
            return response()->json(['error' => 'La carpeta está vacía.'], 404);
        }

        $zipFilePath = $folderPath . '/archivos_comprimidos.zip';
        $zip = new ZipArchive;

        if ($zip->open($zipFilePath, ZipArchive::CREATE) !== TRUE) {
            return response()->json(['error' => 'No se pudo crear el archivo zip.'], 500);
        }

        foreach ($files as $file) {
            $zip->addFile($file->getRealPath(), $file->getFilename());
        }

        $zip->close();

        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }

    public static function eliminarArchivo(string $ruta, string $archivo): bool
    {
        $fullPath = storage_path("app/public/{$ruta}/{$archivo}");

        if (File::exists($fullPath)) {
            return File::delete($fullPath);
        }

        return false;
    }
}
