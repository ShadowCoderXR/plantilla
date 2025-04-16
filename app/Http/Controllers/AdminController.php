<?php

namespace App\Http\Controllers;

use App\Enums\LogUsuarioAccion;
use App\Helpers\Util;
use App\Jobs\GenerarZipDocumentos;
use App\Models\Administrador;
use App\Models\Cliente;
use App\Models\ClienteProveedor;
use App\Models\Descarga;
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
use Illuminate\Support\Facades\Log;

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
        $origen = $request->input('origen', 'administrador');
        $tipo   = (int) $request->input('tipo', 1);
        $anio   = $request->input('anio');
        $mes    = $request->input('mes');

        if ($origen === 'proveedor') {
            $administrador = Administrador::findOrFail($request->administrador_id);
            $cliente       = Cliente::findOrFail($request->cliente_id);
            $proveedor     = Proveedor::findOrFail($request->proveedor_id);
            $tipoDocumento = TipoDocumento::findOrFail($request->tipoDocumento_id);

            $adminSlug   = Util::slugify($administrador->nombre);
            $clienteSlug = Util::slugify($cliente->nombre);
            $proveedorSlug = Util::slugify($proveedor->nombre);
            $tipoSlug = Util::slugify($tipoDocumento->nombre);

            GenerarZipDocumentos::dispatch(
                auth()->id(),
                $adminSlug,
                $clienteSlug,
                $tipo,
                $anio,
                $mes,
                $tipoSlug,
                $proveedorSlug
            );

            $nombreFinal = "{$adminSlug}-{$clienteSlug}-{$tipoSlug}-{$proveedorSlug}";
        }

        elseif ($origen === 'cliente') {
            $administrador = Administrador::findOrFail($request->administrador_id);
            $cliente       = Cliente::findOrFail($request->cliente_id);

            $adminSlug   = Util::slugify($administrador->nombre);
            $clienteSlug = Util::slugify($cliente->nombre);

            GenerarZipDocumentos::dispatch(auth()->id(), $adminSlug, $clienteSlug, $tipo, $anio, $mes);
            $nombreFinal = "{$adminSlug}-{$clienteSlug}";
        }

        else {
            $administrador = Administrador::findOrFail($request->administrador_id);
            $adminSlug     = Util::slugify($administrador->nombre);

            GenerarZipDocumentos::dispatch(auth()->id(), $adminSlug, null, $tipo, $anio, $mes);
            $nombreFinal = $adminSlug;
        }

        return redirect()->route('admin.documentos.descargas');
    }

    public function descargas()
    {
        $descargas = Descarga::where('usuario_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('descargas', compact('descargas'));
    }

    public function zipProgreso($nombre)
    {
        $descarga = Descarga::where('nombre', $nombre)->latest()->first();

        if (!$descarga) {
            return response()->json(['estado' => 'no_encontrado']);
        }

        return response()->json([
            'estado'  => $descarga->estado,
            'ruta'    => $descarga->ruta,
            'tamaño'  => $descarga->tamaño,
            'listo'   => $descarga->estado === 'completado'
        ]);
    }


    public function descargarZip($nombre)
    {
        $ruta = storage_path("app/zips/{$nombre}.zip");

        if (!File::exists($ruta)) abort(404);

        return response()->download($ruta);
    }
}
