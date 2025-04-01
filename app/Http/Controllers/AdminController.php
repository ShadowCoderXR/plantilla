<?php

namespace App\Http\Controllers;

use App\Models\Administrador;
use App\Models\Cliente;
use App\Models\Documento;
use App\Models\DocumentoMatriz;
use App\Models\DocumentoProveedor;
use App\Models\DocumentoRequerido;
use App\Models\GrupoDocumento;
use App\Models\Proveedor;
use App\Models\TipoDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

        return view('administrador', compact('administrador', 'anios'));
    }
    public function proveedores($id)
    {
        $cliente = Cliente::findOrFail($id);
        $proveedores = $cliente->proveedores()->paginate(10);
        return view('partials.proveedores_list', compact('proveedores'));
    }


    public function cliente($id)
    {
        $cliente = Cliente::with('proveedores')->find($id);
        $anios = DocumentoMatriz::select('anio')->distinct()->orderBy('anio', 'desc')->pluck('anio')->toArray();

        return view('cliente', compact('cliente', 'anios'));
    }

    public function proveedor($id, $anio)
    {
        $proveedor = Proveedor::findOrFail($id);
        $clienteIds = $proveedor->clientes()->pluck('clientes.id');
        $anios = DocumentoMatriz::select('anio')->distinct()->orderBy('anio', 'desc')->pluck('anio')->toArray();
        $grupos = GrupoDocumento::all()->keyBy('nombre');

        $clienteProveedorId = DB::table('cliente_proveedor')
            ->where('proveedor_id', $proveedor->id)
            ->whereIn('cliente_id', $clienteIds)
            ->value('id');

        if (!$clienteProveedorId) {
            abort(404, 'Relación cliente-proveedor no encontrada.');
        }

        $documentos = Documento::with('grupo')->get()->groupBy('grupo.nombre');

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
                    'documento' => $doc->nombre,
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

        return view('proveedor', compact('proveedor', 'resultado', 'anio', 'anios', 'grupos'));
    }

    public function documento($id)
    {
        $documentoProveedor = DocumentoProveedor::with('documento', 'clienteProveedor')->findOrFail($id);

        return view('documento', compact('documentoProveedor'));
    }

    public function documentoGuardar(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,xlsx,xls,zip,rar|max:2048',
        ]);

        $archivo = $request->file('file');

        if (!$archivo) {
            return response()->json(['message' => 'No se recibió ningún archivo.'], 400);
        }

        $documento = DocumentoProveedor::with([
            'clienteProveedor.cliente.administradores',
            'clienteProveedor.proveedor',
            'documento'
        ])->findOrFail($id);

        $administrador = $documento->clienteProveedor->cliente->administradores->first()->nombre ?? 'administrador';
        $cliente = $documento->clienteProveedor->cliente->nombre ?? 'cliente';
        $proveedor = $documento->clienteProveedor->proveedor->nombre ?? 'proveedor';

        $administradorSlug = $this->slugify($administrador);
        $clienteSlug = $this->slugify($cliente);
        $proveedorSlug = $this->slugify($proveedor);

        $anio = $documento->anio;
        $mes = $documento->mes;

        $nombreDocumento = $documento->documento->nombre ?? 'documento';
        $nombreNormalizado = $this->slugify($nombreDocumento);
        $extension = $archivo->getClientOriginalExtension();
        $nombreFinal = "{$nombreNormalizado}.{$extension}";

        $ruta = "documentos/{$administradorSlug}/{$clienteSlug}/{$proveedorSlug}/{$anio}/{$mes}";

        if ($documento->ruta && Storage::disk('public')->exists($documento->ruta)) {
            Storage::disk('public')->delete($documento->ruta);
        }

        $rutaArchivo = $archivo->storeAs($ruta, $nombreFinal, 'public');

        $documento->ruta = $rutaArchivo;
        $documento->extension = $extension;
        $documento->estado = DocumentoProveedor::ESTATUS_CARGADO;
        $documento->fecha_carga = now();
        $documento->save();

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

    public function documentoDescargar($id)
    {
        $documento = DocumentoProveedor::findOrFail($id);
        return response()->download(storage_path("app/public/{$documento->ruta}"));
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
