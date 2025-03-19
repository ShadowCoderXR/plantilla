<?php

namespace Database\Seeders;

use App\Models\DocumentoRequerido;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentoRequeridoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documentosPorMes = [
            1 => [2, 3, 4, 5, 6, 7, 9, 11],
            2 => [1, 2, 3, 5, 7, 8, 9],
            3 => [1, 2, 3, 4, 6, 8, 9],
            4 => [1, 2, 3, 4, 7, 8, 9, 10],
            5 => [1, 2, 4, 7, 8, 9, 11],
            6 => [1, 2, 5, 6, 7, 8, 10, 11],
            7 => [1, 3, 4, 5, 6, 7, 9, 10, 11],
            8 => [1, 5, 6, 7, 8, 9, 10, 11],
            9 => [1, 2, 3, 4, 6, 9, 10, 11],
            10 => [1, 3, 4, 7, 8, 9, 10, 11],
            11 => [2, 3, 4, 6, 7, 9, 10, 11],
            12 => [1, 2, 3, 6, 8, 9, 10],
        ];

        $registrosValidos = [];

        foreach ($documentosPorMes as $mes => $documentoIds) {
            foreach ($documentoIds as $documentoId) {
                $registro = DocumentoRequerido::updateOrCreate(
                    ['mes' => $mes, 'tipo_documento_id' => $documentoId]
                );

                $registrosValidos[] = $registro->id;
            }
        }

        DocumentoRequerido::whereNotIn('id', $registrosValidos)->delete();
    }
}
