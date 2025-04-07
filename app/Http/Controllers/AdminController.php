<?php

namespace App\Http\Controllers;

use App\Models\Administrador;
use App\Models\Cliente;
use App\Models\Documento;
use App\Models\DocumentoMatriz;
use App\Models\DocumentoProveedor;
use App\Models\DocumentoProveedorUnicaVez;
use App\Models\GrupoDocumento;
use App\Models\Proveedor;
use App\Models\TipoDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use ZipArchive;

class AdminController extends Controller
{
    public function dashboard()
    {
        $administradores = Administrador::all();

        return view('dashboard', compact('administradores'));
    }

    public function administrador($id)
    {
        $administrador = Administrador::with('clientes.proveedores')->find($id);
        $anios = DocumentoMatriz::select('anio')->distinct()->orderBy('anio', 'desc')->pluck('anio')->toArray();
        $tiposDocumentos = TipoDocumento::all();

        return view('administrador', compact('administrador', 'anios', 'tiposDocumentos'));
    }

    public function cliente($id)
    {
        $cliente = Cliente::with('proveedores')->find($id);
        $anios = DocumentoMatriz::select('anio')->distinct()->orderBy('anio', 'desc')->pluck('anio')->toArray();
        $tiposDocumentos = TipoDocumento::all();

        return view('cliente', compact('cliente', 'anios', 'tiposDocumentos'));
    }

    public function proveedor($id, $anio, $tipo)
    {
        $proveedor = Proveedor::findOrFail($id);
        $clienteIds = $proveedor->clientes()->pluck('clientes.id');
        $anios = DocumentoMatriz::select('anio')->distinct()->orderBy('anio', 'desc')->pluck('anio')->toArray();
        $tipoDocumento = TipoDocumento::findOrFail($tipo);
        $resultadoUnicaVez = DocumentoProveedorUnicaVez::with(['documento', 'clienteProveedor'])->where('cliente_proveedor_id', $id)->get();

        $clienteProveedorId = DB::table('cliente_proveedor')
            ->where('proveedor_id', $proveedor->id)
            ->whereIn('cliente_id', $clienteIds)
            ->value('id');

        if (!$clienteProveedorId) {
            abort(404, 'Relación cliente-proveedor no encontrada.');
        }

        $documentos = Documento::with('grupo')
            ->whereHas('grupo', function ($query) use ($tipo) {
                $query->where('tipo_documento_id', $tipo);
            })
            ->get()
            ->groupBy('grupo.nombre');

        $matriz = DocumentoMatriz::where('anio', $anio)->get()->groupBy('documento_id');

        $documentosProveedor = DocumentoProveedor::where('cliente_proveedor_id', $clienteProveedorId)
            ->where('anio', $anio)
            ->get()
            ->groupBy('documento_id');

        $mesActual = now()->month;
        $anioActual = now()->year;

        $resultado = [];

        foreach ($documentos as $grupo => $docs) {
            foreach ($docs as $doc) {
                $fila = [
                    'id_documento' => $doc->id,
                    'documento' => $doc->nombre,
                    'informacion' => $doc->informacion,
                    'grupo' => $grupo,
                    'meses' => []
                ];

                $mesesNombre = [
                    1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
                    5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
                    9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre',
                ];

                for ($mes = 1; $mes <= 12; $mes++) {
                    $mesNombre = $mesesNombre[$mes];

                    $esObligatorio = $matriz[$doc->id]->firstWhere('mes', $mesNombre)?->obligatorio ?? false;

                    $registro = $documentosProveedor[$doc->id]->firstWhere('mes', $mesNombre);

                    if ($registro) {
                        $fila['meses'][$mes] = [
                            'estado' => $registro->estado,
                            'id' => $registro->id
                        ];
                    } elseif ($esObligatorio) {
                        $estado = ($anio < $anioActual || ($anio == $anioActual && $mes < $mesActual))
                            ? 'faltante'
                            : 'por_cargar';

                        $nuevo = DocumentoProveedor::create([
                            'cliente_proveedor_id' => $clienteProveedorId,
                            'documento_id' => $doc->id,
                            'anio' => $anio,
                            'mes' => $mesNombre,
                            'estado' => $estado
                        ]);

                        $fila['meses'][$mes] = [
                            'estado' => $estado,
                            'id' => $nuevo->id
                        ];
                    } else {
                        $fila['meses'][$mes] = [
                            'estado' => 'no_requerido',
                            'id' => null
                        ];
                    }
                }

                $resultado[] = $fila;
            }
        }

        return view('proveedor', compact('proveedor', 'resultado', 'anio', 'anios', 'tipoDocumento', 'resultadoUnicaVez'));
    }

    public function documento($id, $unicaVez = null)
    {
        if ($unicaVez === 'uv') {
            $documentoProveedor = DocumentoProveedorUnicaVez::with('documento', 'clienteProveedor')->findOrFail($id);
            return view('documento', compact('documentoProveedor', 'unicaVez'));        
        }else if ($unicaVez === null) {
            $documentoProveedor = DocumentoProveedor::with('documento', 'clienteProveedor')->findOrFail($id);
            return view('documento', compact('documentoProveedor'));        
        }else {
            return back()->with('error', 'Error.');
        }

    }

    public function documentoGuardar(Request $request, $id, $unicaVez = null)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,xlsx,xls,sue|max:4096',
        ]);

        $archivo = $request->file('file');

        if (!$archivo) {
            return response()->json(['message' => 'No se recibió ningún archivo.'], 400);
        }

        if ($unicaVez === 'uv'){
            $documento = DocumentoProveedorUnicaVez::with([
                'clienteProveedor.cliente.administradores',
                'clienteProveedor.proveedor',
                'documento.grupo.tipoDocumento'
            ])->findOrFail($id);    
        }else if ($unicaVez === null) {
            $documento = DocumentoProveedor::with([
                'clienteProveedor.cliente.administradores',
                'clienteProveedor.proveedor',
                'documento.grupo.tipoDocumento'
            ])->findOrFail($id);    
        }else {
            return response()->json([
                'message' => 'Error.',
            ], 200);            
        }
        

        $administrador = $documento->clienteProveedor->cliente->administradores->first()->nombre ?? 'administrador';
        $cliente = $documento->clienteProveedor->cliente->nombre ?? 'cliente';
        $proveedor = $documento->clienteProveedor->proveedor->nombre ?? 'proveedor';
        $tipo = $documento->documento->grupo->tipoDocumento->nombre ?? 'tipo';
        $grupo = $documento->documento->grupo->nombre ?? 'grupo';

        $administradorSlug = $this->slugify($administrador);
        $clienteSlug = $this->slugify($cliente);
        $proveedorSlug = $this->slugify($proveedor);
        $tipoSlug = $this->slugify($tipo);
        $grupoSlug = $this->slugify($grupo);
        $anio = $documento->anio;
        $mes = $documento->mes;

        $nombreDocumento = $documento->documento->nombre ?? 'documento';
        $nombreNormalizado = $this->slugify($nombreDocumento);
        //$extension = $archivo->getClientOriginalExtension();
        //$nombreFinal = "{$nombreNormalizado}.{$extension}";
        $nombreFinal = $archivo->getClientOriginalName();

        if($tipoSlug !== $grupoSlug) {
            $tipoSlug = $grupoSlug;
        }

        if ($documento->documento->informacion == 'Documento única vez') {
            $ruta = "documentos/{$administradorSlug}/{$clienteSlug}/{$tipoSlug}/{$proveedorSlug}/única_vez/{$nombreNormalizado}";
        } else {
            $ruta = "documentos/{$administradorSlug}/{$clienteSlug}/{$tipoSlug}/{$proveedorSlug}/{$anio}/{$mes}/{$nombreNormalizado}";
        }

        if ($documento->ruta && Storage::disk('public')->exists($documento->ruta)) {
            Storage::disk('public')->delete($documento->ruta);
        }
        
        $rutaArchivo = $archivo->storeAs($ruta, $nombreFinal, 'public');
        $posicionUltimoSlash = strrpos($rutaArchivo, '/');
        $rutaArchivo = substr($rutaArchivo, 0, $posicionUltimoSlash);

        if (!$documento->ruta){
            $documento->ruta = $rutaArchivo;
            //$documento->extension = $extension;
            $documento->estado = DocumentoProveedor::ESTATUS_CARGADO;
            $documento->fecha_carga = now();
            $documento->save();
        }        

        return response()->json([
            'message' => 'Archivo subido correctamente.',
            'ruta' => $rutaArchivo,
        ], 200);
    }

    private function slugify($string)
    {
        $string = strtolower($string);
        $string = str_replace(' ', '_', $string);
        $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
        $string = preg_replace('/[^a-z0-9_]/', '', $string);
        $string = str_replace('.', '', $string);

        return $string;
    }

    public function documentoDescargar($id, $unicaVez = null)
    {
        if ($unicaVez === 'uv') {
            $documento = DocumentoProveedorUnicaVez::findOrFail($id);
        }else if ($unicaVez === null) {
            $documento = DocumentoProveedor::findOrFail($id);
        }else {
            return back()->with('error', 'Error.');
        }
        
        $folderPath = storage_path("app/public/{$documento->ruta}");

        $zipFileName = 'archivos_comprimidos.zip';
        $zipFilePath = $folderPath . "/" . $zipFileName;

        if (!File::exists($folderPath)) {
            return response()->json(['error' => 'La carpeta no existe.'], 404);
        }

        $files = File::allFiles($folderPath);

        if (count($files) === 0) {
            return response()->json(['error' => 'La carpeta está vacía.'], 404);
        }

        $zip = new ZipArchive;

        if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
            $files = File::allFiles($folderPath);

            foreach ($files as $file) {
                $zip->addFile($file->getRealPath(), $file->getFilename());
            }

            $zip->close();

            return response()->download($zipFilePath)->deleteFileAfterSend(true);
        } else {
            return response()->json(['error' => 'No se pudo crear el archivo zip.'], 500);
        }        

        
    }

    public function generarZip(Request $request)
    {
        $zip = new \ZipArchive();
        $zipFileName = 'documentos.zip';

        $zipPath = storage_path('app/public/' . $zipFileName);

        $folderPath = storage_path('app/public/documentos');

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($folderPath),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();

                    $relativePath = 'documentos/' . substr($filePath, strlen($folderPath) + 1);

                    $zip->addFile($filePath, $relativePath);
                }
            }

            $zip->close();

            return response()->download($zipPath)->deleteFileAfterSend(true);
        }

        return response()->json(['error' => 'No se pudo crear el archivo ZIP'], 500);
    }
}
