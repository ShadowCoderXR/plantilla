<?php

namespace Database\Seeders;

use App\Models\TipoDocumento;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoDocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documentos = [
            'Comprobante de aplicación de pago de cuotas',
            'Comprobante de pago de RCV (Retro, Cesantía y Vejez) o INFONAVIT',
            'CDFI Nómina (XML)',
            'Acuse de recibo de la declaración de IVA',
            'Acuse de recibo de la declaración de ISR',
            'Comprobante Bancario de Aplicación de la declaración de IVA',
            'Comprobante Bancario de Aplicación de la declaración de ISR',
            'SISUB',
            'ICSOE',
            'SUA',
            'Lista de asistencia de personal que ejecutó el servicio',
        ];

        foreach ($documentos as $documento) {
            TipoDocumento::updateOrCreate(
                ['nombre' => $documento],
                ['nombre' => $documento]
            );
        }

        TipoDocumento::whereNotIn('nombre', $documentos)->delete();
    }
}
