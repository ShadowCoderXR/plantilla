<?php

namespace App\Http\Controllers;

use App\Enums\LogUsuarioAccion;
use App\Helpers\Util;
use App\Jobs\GenerarZipDocumentos;
use App\Models\Administrador;
use App\Models\Cliente;
use App\Models\ClienteProveedor;
use App\Models\Documento;
use App\Models\DocumentoMatriz;
use App\Models\DocumentoProveedor;
use App\Models\DocumentoProveedorUnicaVez;
use App\Models\Proveedor;
use App\Models\TipoDocumento;
use App\Services\DocumentoService;
use App\Services\LogUsuarioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AdminController extends Controller
{
    public function dashboard()
    {
        $administradores = Administrador::all();

        return LogUsuarioService::logRespuesta([
            'accion' => LogUsuarioAccion::DASHBOARD,
            'descripcion' => 'Acceso al dashboard',
            'vista' => 'dashboard',
            'data' => compact('administradores'),
        ]);
    }

    public function administrador($id)
    {
        $administrador = Administrador::find($id);
        $anios = DocumentoMatriz::select('anio')->distinct()->orderBy('anio', 'desc')->pluck('anio')->toArray();
        $tiposDocumentos = TipoDocumento::all();

        return LogUsuarioService::logRespuesta([
            'accion'      => LogUsuarioAccion::ADMINISTRADOR,
            'descripcion' => "El usuario accedió al administrador: {$administrador->nombre} (ID: {$id})",
            'vista'       => 'administrador',
            'data'        => compact('administrador', 'anios', 'tiposDocumentos'),
        ]);
    }

    public function cliente($id)
    {
        $cliente = Cliente::findOrFail($id);
        $anios = DocumentoMatriz::select('anio')->distinct()->orderBy('anio', 'desc')->pluck('anio')->toArray();
        $tiposDocumentos = TipoDocumento::all();

        return LogUsuarioService::logRespuesta([
            'accion'      => LogUsuarioAccion::CLIENTE,
            'descripcion' => "Acceso a vista del cliente: {$cliente->nombre} (ID: {$cliente->id})",
            'vista'       => 'cliente',
            'data'        => compact('cliente', 'anios', 'tiposDocumentos'),
        ]);
    }

    public function proveedor($idProveedor, $idCliente, $anio, $tipo)
    {
        $proveedor = Proveedor::findOrFail($idProveedor);
        $tipoDocumento = TipoDocumento::findOrFail($tipo);

        $clienteProveedor = ClienteProveedor::obtenerRelacion($idCliente, $proveedor->id);
        if (!$clienteProveedor) {
            abort(404, 'Relación cliente-proveedor no encontrada.');
        }

        $anios = DocumentoMatriz::aniosDisponibles();

        $resultadoUnicaVez = DocumentoProveedorUnicaVez::with(['documento', 'clienteProveedor'])
            ->where('cliente_proveedor_id', $clienteProveedor->id)
            ->get();

        $documentos = Documento::porTipoAgrupadoPorGrupo($tipo);
        $matriz = DocumentoMatriz::porAnioAgrupado($anio);
        $documentosProveedor = DocumentoProveedor::porRelacionYAnioAgrupado($clienteProveedor->id, $anio);

        $resultado = DocumentoService::generarResultadoMensual(
            $documentos,
            $matriz,
            $documentosProveedor,
            $clienteProveedor,
            $anio
        );

        return LogUsuarioService::logRespuesta([
            'accion'      => LogUsuarioAccion::PROVEEDORES,
            'descripcion' => "Vista proveedor ID {$proveedor->id}, cliente ID {$idCliente}, año {$anio}, tipo {$tipo}",
            'vista'       => 'proveedor',
            'data'        => compact(
                'proveedor',
                'resultado',
                'anio',
                'anios',
                'tipoDocumento',
                'resultadoUnicaVez',
                'clienteProveedor'
            ),
        ]);
    }


    public function documento($id, $unicaVez = null)
    {
        try {
            $documentoProveedor = DocumentoService::obtenerDocumentoProveedor($id, $unicaVez);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', 'Error.');
        }

        $archivos = DocumentoService::obtenerArchivos($documentoProveedor->ruta);

        DocumentoService::actualizarEstadoSiNoHayArchivos($documentoProveedor, $archivos, $unicaVez);

        return LogUsuarioService::logRespuesta([
            'accion'      => LogUsuarioAccion::DOCUMENTOS,
            'descripcion' => "Acceso a documento ID {$id}" . ($unicaVez === 'uv' ? ' (única vez)' : ''),
            'vista'       => 'documento',
            'data'        => compact('documentoProveedor', 'unicaVez', 'archivos'),
        ]);
    }

    public function documentoGuardar(Request $request, $id, $unicaVez = null)
    {
        try {
            $resultado = DocumentoService::guardarDocumento($request, $id, $unicaVez);
        } catch (\InvalidArgumentException $e) {
            return LogUsuarioService::logRespuesta([
                'accion'      => LogUsuarioAccion::SUBIR_DOCUMENTO,
                'descripcion' => 'Error al subir documento (parámetro inválido)',
                'respuesta'   => response()->json(['message' => 'Error.'], 400),
            ]);
        }

        if ($resultado['error']) {
            return LogUsuarioService::logRespuesta([
                'accion'      => LogUsuarioAccion::SUBIR_DOCUMENTO,
                'descripcion' => "Error al subir documento ID {$id}" . ($unicaVez === 'uv' ? ' (única vez)' : ''),
                'respuesta'   => response()->json(['message' => $resultado['message']], $resultado['status']),
            ]);
        }

        return LogUsuarioService::logRespuesta([
            'accion'      => LogUsuarioAccion::SUBIR_DOCUMENTO,
            'descripcion' => "Subida de documento ID {$id}" . ($unicaVez === 'uv' ? ' (única vez)' : ''),
            'respuesta'   => response()->json([
                'message' => $resultado['message'],
                'ruta'    => $resultado['ruta'],
            ], $resultado['status']),
        ]);
    }

    public function documentoDescargar($id, $unicaVez = null)
    {
        try {
            $respuesta = DocumentoService::descargarYComprimir($id, $unicaVez);
        } catch (\InvalidArgumentException $e) {
            return LogUsuarioService::logRespuesta([
                'accion'      => LogUsuarioAccion::DESCARGAR_DOCUMENTO,
                'descripcion' => 'Error al descargar documento (parámetro inválido)',
                'respuesta'   => response()->json(['error' => 'Error al procesar la descarga.'], 400),
            ]);
        }

        return LogUsuarioService::logRespuesta([
            'accion'      => LogUsuarioAccion::DESCARGAR_DOCUMENTO,
            'descripcion' => "Descarga de documento ID {$id}" . ($unicaVez === 'uv' ? ' (única vez)' : ''),
            'respuesta'   => $respuesta,
        ]);
    }

    public function eliminarArchivo(Request $request)
    {
        $request->validate([
            'ruta'   => 'required|string',
            'archivo'=> 'required|string',
        ]);

        $exito = DocumentoService::eliminarArchivo($request->ruta, $request->archivo);

        return LogUsuarioService::logRespuesta([
            'accion'      => LogUsuarioAccion::ELIMINAR_DOCUMENTO,
            'descripcion' => "Eliminación de archivo: {$request->archivo} en ruta {$request->ruta}",
            'respuesta'   => back()->with($exito ? 'success' : 'error', $exito ? 'Archivo eliminado correctamente.' : 'Archivo no encontrado.'),
        ]);
    }

    public function generarZip(Request $request)
    {
        $administrador = Administrador::find($request->administrador_id);
        $administradorSlugify = Util::slugify($administrador->nombre);

        GenerarZipDocumentos::dispatch($administradorSlugify);

        return redirect()->route('admin.documentos.zip.esperando', ['nombre' => $administradorSlugify]);
    }


    public function zipProgreso($nombre)
    {
        $nombre = Util::slugify($nombre);
        $ruta = storage_path("app/zips/{$nombre}.zip");
        return response()->json(['listo' => File::exists($ruta)]);
    }


    public function descargarZip($nombre)
    {
        $nombre = Util::slugify($nombre);
        $ruta = storage_path("app/zips/{$nombre}.zip");

        if (!File::exists($ruta)) abort(404);

        return response()->download($ruta);
    }

    public function esperandoVista($nombre)
    {
        return view('esperando', compact('nombre'));
    }

}
