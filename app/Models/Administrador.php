<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Administrador extends Model
{
    protected $table = 'administradores';
    protected $fillable = [
        'nombre',
        'descripcion',
        'logo',
        'small_logo',
        'color',
        'telefono',
        'correo',
        'descripcion_adicional',
    ];

    public function clientes()
    {
        return $this->hasMany(Cliente::class);
    }

    public function getNumeroClientesAttribute()
    {
        return $this->clientes->count();
    }
}
