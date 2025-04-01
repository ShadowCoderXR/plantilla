<?php

namespace Database\Seeders;

use App\Models\ClienteProveedor;
use App\Models\DocumentoMatriz;
use App\Models\DocumentoProveedor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DocumentoProveedorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $anioActual = $now->year;
        $mesActualNum = $now->month;

        $mesesNombreInvertido = [];
        for ($i = 1; $i <= 12; $i++) {
            $nombre = strtolower(Carbon::create()->month($i)->translatedFormat('F'));
            $mesesNombreInvertido[$nombre] = $i;
        }

        $relaciones = ClienteProveedor::all();
        $matriz = DocumentoMatriz::all();

        foreach ($relaciones as $relacion) {
            foreach ($matriz as $entrada) {
                $documentoId = $entrada->documento_id;
                $anio = $entrada->anio;
                $mes = strtolower($entrada->mes);
                $esObligatorio = $entrada->obligatorio;

                if (!$esObligatorio) {
                    DocumentoProveedor::firstOrCreate([
                        'cliente_proveedor_id' => $relacion->id,
                        'documento_id' => $documentoId,
                        'mes' => $mes,
                        'anio' => $anio,
                    ], [
                        'estado' => DocumentoProveedor::ESTATUS_NO_REQUERIDO,
                    ]);
                    continue;
                }

                $existe = DocumentoProveedor::where([
                    'cliente_proveedor_id' => $relacion->id,
                    'documento_id' => $documentoId,
                    'mes' => $mes,
                    'anio' => $anio,
                ])->exists();

                if (!$existe) {
                    $mesEvaluado = $mesesNombreInvertido[$mes];

                    $estado = match (true) {
                        $anio < $anioActual || ($anio == $anioActual && $mesEvaluado < $mesActualNum) => DocumentoProveedor::ESTATUS_FALTANTE,
                        $anio == $anioActual && $mesEvaluado == $mesActualNum => DocumentoProveedor::ESTATUS_POR_CARGAR,
                        $anio > $anioActual || ($anio == $anioActual && $mesEvaluado > $mesActualNum) => DocumentoProveedor::ESTATUS_FALTANTE,
                        default => DocumentoProveedor::ESTATUS_POR_CARGAR,
                    };

                    DocumentoProveedor::create([
                        'cliente_proveedor_id' => $relacion->id,
                        'documento_id' => $documentoId,
                        'mes' => $mes,
                        'anio' => $anio,
                        'estado' => $estado,
                    ]);
                }
            }
        }
    }
}
