<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $table = 'documentos';

    protected $fillable = [
        'grupo_documento_id',
        'nombre',
        'informacion',
    ];

    public function grupo()
    {
        return $this->belongsTo(GrupoDocumento::class, 'grupo_documento_id');
    }

    public function matriz()
    {
        return $this->hasMany(DocumentoMatriz::class, 'documento_id');
    }

    public function documentosProveedor()
    {
        return $this->hasMany(DocumentoProveedor::class, 'documento_id');
    }
}
