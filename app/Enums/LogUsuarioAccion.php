<?php

namespace App\Enums;

enum LogUsuarioAccion: string
{
    // Sesiones
    case LOGIN = 'login';
    case LOGOUT = 'logout';

    // Documentos
    case SUBIR_DOCUMENTO = 'subir_documento';
    case DESCARGAR_DOCUMENTO = 'descargar_documento';
    case ELIMINAR_DOCUMENTO = 'eliminar_documento';

    // Navegación
    case DASHBOARD = 'dashboard';
    case ADMINISTRADOR = 'administrador';
    case CLIENTE = 'cliente';
    case PROVEEDORES = 'proveedores';
    case DOCUMENTOS = 'documentos';

    public function tipo(): string
    {
        return match($this) {
            self::LOGIN,
            self::LOGOUT => 'sesion',
            self::SUBIR_DOCUMENTO,
            self::DESCARGAR_DOCUMENTO,
            self::ELIMINAR_DOCUMENTO => 'documento',
            self::DASHBOARD,
            self::ADMINISTRADOR,
            self::CLIENTE,
            self::PROVEEDORES,
            self::DOCUMENTOS => 'navegacion',
        };
    }
}
