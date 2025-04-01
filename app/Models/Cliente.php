<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';
    protected $fillable = [
        'nombre',
        'descripcion',
        'logo',
        'small_logo',
        'color',
        'telefono',
        'correo',
        'descripcion_adicional',
        'administrador_id',
    ];

    public function administradores()
    {
        return $this->belongsToMany(Administrador::class);
    }

    public function proveedores()
    {
        return $this->belongsToMany(Proveedor::class);
    }
}
