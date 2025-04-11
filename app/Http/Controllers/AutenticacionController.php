<?php

namespace App\Http\Controllers;

use App\Enums\LogUsuarioAccion;
use App\Models\Usuario;
use App\Services\LogUsuarioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AutenticacionController extends Controller
{
    public function formulario()
    {
        return view('inicio-sesion');
    }

    public function iniciarSesion(Request $request)
    {
        $request->validate([
            'correo' => ['required', 'email'],
            'contrasena' => ['required'],
        ]);

        $usuario = Usuario::where('correo', $request->correo)->first();

        if (!$usuario) {
            return back()->withErrors([
                'correo' => 'El correo no está registrado.',
            ])->withInput();
        }

        if (!Hash::check($request->contrasena, $usuario->contrasena)) {
            return back()->withErrors([
                'contrasena' => 'La contraseña es incorrecta.',
            ])->withInput();
        }

        LogUsuarioService::registrar(
            $request,
            LogUsuarioAccion::LOGIN,
            'Inicio de sesión',
        );

        Auth::guard('web')->login($usuario);
        $request->session()->regenerate();
        return redirect()->intended('admin/dashboard');
    }


    public function cerrarSesion(Request $request)
    {

        $usuario = Auth::user();

        LogUsuarioService::registrar(
            $request,
            LogUsuarioAccion::LOGOUT,
            'Cierre de sesión',
        );

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('inicio-sesion');
    }
}
