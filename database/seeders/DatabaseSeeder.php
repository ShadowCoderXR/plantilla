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
            GrupoDocumentoSeeder::class,
            DocumentoSeeder::class,
            DocumentoMatrizSeeder::class,
            DocumentoProveedorSeeder::class,
        ]);
    }
}
