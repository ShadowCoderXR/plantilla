<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores';
    protected $fillable = [
        'nombre',
        'descripcion',
        'logo',
        'small_logo',
        'color',
        'telefono',
        'correo',
        'descripcion_adicional',
        'cliente_id',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class);
    }
}
