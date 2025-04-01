<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Proveedor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProveedorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('data/proveedores.csv');

        if (!File::exists($path)) {
            $this->command->error("No se encontró el archivo proveedores.csv en database/data");
            return;
        }

        $file = fopen($path, 'r');
        $headers = fgetcsv($file);

        $proveedoresEnCsv = [];
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

            if (!isset($data['nombre']) || !isset($data['cliente'])) {
                continue;
            }

            $cliente = Cliente::where('nombre', $data['cliente'])->first();
            if (!$cliente) {
                $this->command->warn("Cliente no encontrado: {$data['cliente']}");
                continue;
            }

            $proveedorData = $data;
            unset($proveedorData['cliente']);

            if (!in_array($data['nombre'], $procesados)) {
                $proveedor = Proveedor::updateOrCreate(
                    ['nombre' => $data['nombre']],
                    $proveedorData
                );
                $procesados[] = $data['nombre'];
            } else {
                $proveedor = Proveedor::where('nombre', $data['nombre'])->first();
            }


            $proveedoresEnCsv[] = $proveedor->nombre;
            $relacionesEnCsv[] = ['proveedor_id' => $proveedor->id, 'cliente_id' => $cliente->id];

            $proveedor->clientes()->syncWithoutDetaching([
                $cliente->id => ['created_at' => now(), 'updated_at' => now()]
            ]);
        }

        fclose($file);

        Proveedor::whereNotIn('nombre', $proveedoresEnCsv)->delete();

        $todos = DB::table('cliente_proveedor')->get();

        foreach ($todos as $relacion) {
            $existe = collect($relacionesEnCsv)->contains(function ($rel) use ($relacion) {
                return $rel['proveedor_id'] == $relacion->proveedor_id && $rel['cliente_id'] == $relacion->cliente_id;
            });

            if (!$existe) {
                DB::table('cliente_proveedor')
                    ->where('proveedor_id', $relacion->proveedor_id)
                    ->where('cliente_id', $relacion->cliente_id)
                    ->delete();
            }
        }
    }
}
