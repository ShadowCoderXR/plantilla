<?php

namespace App\Services;

use App\Enums\LogUsuarioAccion;
use App\Models\LogUsuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogUsuarioService
{
    public static function logRespuesta(array $params)
    {
        $accion      = $params['accion'] ?? null;
        $descripcion = $params['descripcion'] ?? null;
        $respuesta   = $params['respuesta'] ?? null;
        $vista       = $params['vista'] ?? null;
        $data        = $params['data'] ?? [];

        if (!$accion instanceof LogUsuarioAccion) {
            throw new \InvalidArgumentException('El parámetro "accion" es obligatorio y debe ser una instancia de LogUsuarioAccion.');
        }

        self::registrar(request(), $accion, $descripcion);

        return match (true) {
            !is_null($respuesta) => $respuesta,
            !is_null($vista)     => view($vista, $data),
            default              => throw new \InvalidArgumentException('Se requiere al menos "respuesta" o "vista".')
        };
    }

    public static function registrar(Request $request, LogUsuarioAccion $accion, string $descripcion = null): void
    {
        $usuario = Auth::user();

        LogUsuario::create([
            'user_email'   => $usuario ? $usuario->correo : $request->input('correo'),
            'user_ip'      => self::obtenerIpReal($request),
            'user_agent'   => $request->header('User-Agent'),
            'action'       => $accion->value,
            'action_type'  => $accion->tipo(),
            'description'  => $descripcion,
        ]);
    }

    protected static function obtenerIpReal(Request $request): string
    {
        foreach ([
                     'X-Forwarded-For',
                     'X-Real-IP',
                     'CF-Connecting-IP',
                     'True-Client-IP',
                 ] as $header) {
            if ($request->headers->has($header)) {
                return explode(',', $request->header($header))[0];
            }
        }

        return $request->ip();
    }
}
