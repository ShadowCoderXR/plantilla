<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoProveedorUnicaVez extends Model
{
    protected $table = 'documentos_proveedor_unica_vez';

    protected $fillable = [
        'cliente_proveedor_id',
        'documento_id',
        'mes',
        'anio',
        'ruta',
        'estado',
    ];

    public const ESTATUS_CARGADO = 'cargado';
    public const ESTATUS_POR_CARGAR = 'por_cargar';
    public const ESTATUS_FALTANTE = 'faltante';
    public const ESTATUS_NO_REQUERIDO = 'no_requerido';

    public static function estatusList(): array
    {
        return [
            self::ESTATUS_CARGADO,
            self::ESTATUS_POR_CARGAR,
            self::ESTATUS_FALTANTE,
            self::ESTATUS_NO_REQUERIDO,
        ];
    }

    public function clienteProveedor()
    {
        return $this->belongsTo(ClienteProveedor::class, 'cliente_proveedor_id');
    }

    public function documento()
    {
        return $this->belongsTo(Documento::class, 'documento_id');
    }
}
