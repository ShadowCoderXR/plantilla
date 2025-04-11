<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoProveedor extends Model
{
    protected $table = 'documentos_proveedor';

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

    public const MESES = [
        1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
        5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
        9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre',
    ];

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

    public static function porRelacionYAnioAgrupado(int $clienteProveedorId, int $anio)
    {
        return self::where('cliente_proveedor_id', $clienteProveedorId)
            ->where('anio', $anio)
            ->get()
            ->groupBy('documento_id');
    }

    public static function estadoSegunFecha(int $anio, int $mes): string
    {
        $ahora = now();

        return ($anio < $ahora->year || ($anio === $ahora->year && $mes < $ahora->month))
            ? self::ESTATUS_FALTANTE
            : self::ESTATUS_POR_CARGAR;
    }

}
