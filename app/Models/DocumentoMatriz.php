<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoMatriz extends Model
{
    protected $table = 'documentos_matriz';

    protected $fillable = [
        'documento_id',
        'mes',
        'anio',
        'obligatorio',
    ];

    public function documento()
    {
        return $this->belongsTo(Documento::class, 'documento_id');
    }

    public static function aniosDisponibles(): array
    {
        return self::select('anio')
            ->distinct()
            ->orderByDesc('anio')
            ->pluck('anio')
            ->toArray();
    }

    public static function porAnioAgrupado(int $anio)
    {
        return self::where('anio', $anio)->get()->groupBy('documento_id');
    }
}
