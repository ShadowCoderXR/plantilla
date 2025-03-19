<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $table = 'documentos';
    protected $fillable = [
        'proveedor_id',
        'tipo_documento_id',
        'mes',
        'archivo',
        'estado'
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class);
    }

    public function getEstadoTextoAttribute()
    {
        $estados = [
            1 => 'Presente',
            2 => 'Subir',
            3 => 'Faltante',
            4 => 'No requerido'
        ];

        return $estados[$this->estado] ?? 'Desconocido';
    }

    public function getMesesTextoAttribute()
    {
        $meses = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        ];

        return $meses[$this->mes] ?? 'Desconocido';
    }
}
