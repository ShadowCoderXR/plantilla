<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoRequerido extends Model
{

    protected $table = 'documentos_requeridos';

    protected $fillable = [
        'tipo_documento_id',
        'mes'
    ];

    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class);
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
