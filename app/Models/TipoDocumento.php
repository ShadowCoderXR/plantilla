<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    protected $table = 'tipos_documento';
    protected $fillable = ['nombre'];

    public function documentosRequeridos()
    {
        return $this->hasMany(DocumentoRequerido::class);
    }
}
