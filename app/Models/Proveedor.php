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

    public function clientes()
    {
        return $this->belongsToMany(Cliente::class);
    }
}
