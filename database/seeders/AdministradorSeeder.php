<?php

namespace Database\Seeders;

use App\Models\Administrador;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class AdministradorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('data/administradores.csv');

        if (!File::exists($path)) {
            $this->command->error("No se encontró el archivo administradores.csv en database/data");
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

            if (!isset($data['nombre'])) {
                continue;
            }

            $nombres[] = $data['nombre'];

            Administrador::updateOrCreate(
                ['nombre' => $data['nombre']],
                $data
            );
        }

        fclose($file);

        Administrador::whereNotIn('nombre', $nombres)->delete();
    }
}
