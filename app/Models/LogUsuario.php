<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogUsuario extends Model
{
    protected $table = 'log_usuarios';

    protected $fillable = [
        'user_email',
        'user_ip',
        'user_agent',
        'action',
        'action_type',
        'description',
    ];
}
