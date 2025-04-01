<?php

namespace Database\Seeders;

use App\Models\Administrador;
use App\Models\Cliente;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('data/clientes.csv');

        if (!File::exists($path)) {
            $this->command->error("No se encontró el archivo clientes.csv en database/data");
            return;
        }

        $file = fopen($path, 'r');
        $headers = fgetcsv($file);

        $nombres = [];
        $relacionesEnCsv = [];
        $procesados = [];

        while (($row = fgetcsv($file)) !== false) {
            $data = [];

            foreach ($headers as $index => $header) {
                $key = trim($header);
                $value = isset($row[$index]) ? trim(preg_replace('/\xC2\xA0|\s+/u', ' ', $row[$index])) : null;

                if ($value !== null && $value !== '') {
                    $data[$key] = $value;
                }
            }

            if (!isset($data['nombre']) || !isset($data['administrador'])) {
                continue;
            }

            $admin = Administrador::where('nombre', $data['administrador'])->first();
            if (!$admin) {
                $this->command->warn("Administrador no encontrado: {$data['administrador']}");
                continue;
            }

            $clienteData = $data;
            unset($clienteData['administrador']);

            if (!in_array($data['nombre'], $procesados)) {
                $cliente = Cliente::updateOrCreate(
                    ['nombre' => $data['nombre']],
                    $clienteData
                );
                $procesados[] = $data['nombre'];
            } else {
                $cliente = Cliente::where('nombre', $data['nombre'])->first();
            }


            $nombres[] = $cliente->nombre;
            $relacionesEnCsv[] = ['cliente_id' => $cliente->id, 'administrador_id' => $admin->id];

            $cliente->administradores()->syncWithoutDetaching([
                $admin->id => ['created_at' => now(), 'updated_at' => now()]
            ]);
        }

        fclose($file);

        Cliente::whereNotIn('nombre', $nombres)->delete();

        $todas = DB::table('administrador_cliente')->get();

        foreach ($todas as $relacion) {
            $existe = collect($relacionesEnCsv)->contains(function ($r) use ($relacion) {
                return $r['cliente_id'] == $relacion->cliente_id && $r['administrador_id'] == $relacion->administrador_id;
            });

            if (!$existe) {
                DB::table('administrador_cliente')
                    ->where('cliente_id', $relacion->cliente_id)
                    ->where('administrador_id', $relacion->administrador_id)
                    ->delete();
            }
        }
    }
}
