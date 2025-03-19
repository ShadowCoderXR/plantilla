<?php

namespace Database\Seeders;

use App\Models\Documento;
use App\Models\DocumentoRequerido;
use App\Models\Proveedor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $proveedores = Proveedor::all();
        $documentosValidos = [];

        $años = range(2021, 2025);

        foreach ($años as $anio) {
            for ($mes = 1; $mes <= 12; $mes++) {
                foreach ($proveedores as $proveedor) {
                    $documentosRequeridos = DocumentoRequerido::where('mes', $mes)->pluck('tipo_documento_id')->toArray();

                    $todosLosDocumentos = DocumentoRequerido::distinct()->pluck('tipo_documento_id')->toArray();

                    foreach ($todosLosDocumentos as $tipoDocumentoId) {
                        $estado = in_array($tipoDocumentoId, $documentosRequeridos) ? '2' : '4';

                        $documento = Documento::updateOrCreate(
                            [
                                'proveedor_id' => $proveedor->id,
                                'tipo_documento_id' => $tipoDocumentoId,
                                'mes' => $mes,
                                'anio' => $anio,
                            ],
                            [
                                'estado' => $estado,
                            ]
                        );
                    }
                }
            }
        }
    }
}
