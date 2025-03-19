<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdministradorSeeder::class,
            ClienteSeeder::class,
            ProveedorSeeder::class,
            TipoDocumentoSeeder::class,
            DocumentoRequeridoSeeder::class,
            DocumentoSeeder::class,
        ]);
    }
}
