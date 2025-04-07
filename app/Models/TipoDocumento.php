<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    protected $table = 'tipos_documentos';

    protected $fillable = [
        'nombre',
    ];

    public function gruposDocumentos()
    {
        return $this->hasMany(GrupoDocumento::class, 'tipo_documento_id');
    }
}
