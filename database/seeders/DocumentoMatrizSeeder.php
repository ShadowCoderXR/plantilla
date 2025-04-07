<?php

namespace Database\Seeders;

use App\Models\Documento;
use App\Models\DocumentoMatriz;
use App\Models\GrupoDocumento;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DocumentoMatrizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pathDir = database_path('data/documentos-matriz');

        if (!File::isDirectory($pathDir)) {
            $this->command->error("No se encontró la carpeta documentos-matriz en database/data");
            return;
        }

        $matricesDesdeCSV = [];

        foreach (File::files($pathDir) as $file) {
            $filename = $file->getFilename();

            if (!preg_match('/(\d{4})/', $filename, $match)) {
                $this->command->warn("No se pudo extraer el año del archivo: $filename");
                continue;
            }

            $anio = $match[1];
            $filePath = $file->getPathname();

            $csv = fopen($filePath, 'r');
            $headers = fgetcsv($csv);

            while (($row = fgetcsv($csv)) !== false) {
                $data = [];

                foreach ($headers as $index => $header) {
                    $key = trim($header);
                    $value = isset($row[$index]) ? trim(preg_replace('/\xC2\xA0|\s+/u', ' ', $row[$index])) : null;

                    if ($value !== null && $value !== '') {
                        $data[$key] = $value;
                    }
                }

                if (!isset($data['grupo']) || !isset($data['documento'])) {
                    continue;
                }

                $grupo = GrupoDocumento::where('nombre', $data['grupo'])->first();
                if (!$grupo) {
                    $this->command->warn("Grupo no encontrado: {$data['grupo']}");
                    continue;
                }

                $documento = Documento::where('nombre', $data['documento'])
                    ->where('grupo_documento_id', $grupo->id)
                    ->first();

                if (!$documento) {
                    $this->command->warn("Documento no encontrado: {$data['documento']} en grupo {$data['grupo']}");
                    continue;
                }

                foreach ($data as $mes => $valor) {
                    if (in_array($mes, ['documento', 'grupo', 'informacion'])) {
                        continue;
                    }

                    $mes = strtolower($mes);
                    $obligatorio = strtolower($valor) === 'sí';

                    DocumentoMatriz::updateOrCreate(
                        [
                            'documento_id' => $documento->id,
                            'mes' => $mes,
                            'anio' => $anio
                        ],
                        [
                            'obligatorio' => $obligatorio
                        ]
                    );

                    $matricesDesdeCSV[] = [
                        'documento_id' => $documento->id,
                        'mes' => $mes,
                        'anio' => $anio
                    ];
                }
            }

            fclose($csv);
        }

        $todas = DocumentoMatriz::all();

        foreach ($todas as $matriz) {
            $existe = collect($matricesDesdeCSV)->contains(function ($m) use ($matriz) {
                return $m['documento_id'] == $matriz->documento_id
                    && $m['mes'] == $matriz->mes
                    && $m['anio'] == $matriz->anio;
            });

            if (!$existe) {
                $matriz->delete();
            }
        }
    }
}
