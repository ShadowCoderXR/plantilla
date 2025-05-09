<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model implements AuthenticatableContract
{
    use Authenticatable;

    protected $table = 'usuarios';

    protected $fillable = [
        'correo',
        'contrasena',
    ];

    protected $hidden = [
        'contrasena',
    ];

    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    public function descargas()
    {
        return $this->hasMany(Descarga::class, 'usuario_id');
    }
}
