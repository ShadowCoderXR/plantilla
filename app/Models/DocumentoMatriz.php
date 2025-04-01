<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoMatriz extends Model
{
    protected $table = 'documentos_matriz';

    protected $fillable = [
        'documento_id',
        'mes',
        'anio',
        'obligatorio',
    ];

    public function documento()
    {
        return $this->belongsTo(Documento::class, 'documento_id');
    }
}
