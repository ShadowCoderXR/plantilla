<?php

namespace Database\Seeders;

use App\Models\GrupoDocumento;
use App\Models\TipoDocumento;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class GrupoDocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('data/grupos_documentos.csv');

        if (!File::exists($path)) {
            $this->command->error("No se encontró el archivo documentos.csv en database/data");
            return;
        }

        $file = fopen($path, 'r');
        $headers = fgetcsv($file);
        $nombres = [];

        while (($row = fgetcsv($file)) !== false) {
            $data = [];

            foreach ($headers as $index => $header) {
                $key = trim($header);
                $value = isset($row[$index]) ? trim(preg_replace('/\xC2\xA0|\s+/u', ' ', $row[$index])) : null;

                if ($value !== null && $value !== '') {
                    $data[$key] = $value;
                }
            }

            if (!isset($data['nombre']) || !isset($data['tipo'])) {
                continue;
            }

            $tipo = TipoDocumento::where('nombre', $data['tipo'])->first();
            if (!$tipo) {
                $this->command->warn("Tipo no encontrado: {$data['tipo']}");
                continue;
            }

            $grupoData = [
                'tipo_documento_id' => $tipo->id,
                'descripcion' => $data['descripcion'] ?? null,
            ];

            $nombres[] = $data['nombre'];

            GrupoDocumento::updateOrCreate(
                ['nombre' => $data['nombre']],
                $grupoData
            );
        }

        fclose($file);

        GrupoDocumento::whereNotIn('nombre', $nombres)->delete();
    }
}
