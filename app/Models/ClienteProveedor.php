<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteProveedor extends Model
{
    protected $table = 'cliente_proveedor';

    public function documentos()
    {
        return $this->hasMany(DocumentoProveedor::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }
}
