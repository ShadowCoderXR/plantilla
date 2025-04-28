<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Descarga extends Model
{
    protected $fillable = [
        'usuario_id',
        'nombre',
        'ruta',
        'estado',
        'tamaño',
        'descripcion',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
