<?php

namespace App\Http\Controllers;

use App\Models\Administrador;
use App\Models\Cliente;
use App\Models\Documento;
use App\Models\DocumentoRequerido;
use App\Models\Proveedor;
use App\Models\TipoDocumento;
use Illuminate\Http\Request;

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

        return view('administrador', compact('administrador'));
    }

    public function cliente($id)
    {
        $cliente = Cliente::with('proveedores')->find($id);

        return view('cliente', compact('cliente'));
    }

    public function proveedor($id, $año)
    {
        $mesActual = now()->month;

        $proveedor = Proveedor::findOrFail($id);

        $documentos = Documento::findOrFail($id)->where('anio', $año)->where('proveedor_id', $id)->first();
        dd($documentos);

        $tipoDocumentos = TipoDocumento::all();
        dd($tipoDocumentos);
        

//        armar matriz




        return view('proveedor', compact('proveedor', 'documentos', 'año'));
    }

    public function documento($status)
    {
        return view('documento', ['status' => $status]);
    }
}
