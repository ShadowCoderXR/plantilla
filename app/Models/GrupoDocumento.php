<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrupoDocumento extends Model
{
    protected $table = 'grupos_documentos';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function documentos()
    {
        return $this->hasMany(Documento::class, 'grupo_documento_id');
    }

    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class, 'tipo_documento_id');
    }
}
