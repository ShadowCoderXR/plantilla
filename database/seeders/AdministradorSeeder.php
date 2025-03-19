<?php

namespace Database\Seeders;

use App\Models\Administrador;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdministradorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $administradores = [
            [
                'nombre' => 'VP360',
                'descripcion' => 'Soluciones Tecnológicas - Innovación y Desarrollo',
                'logo' => 'img/logos/vp360.png',
                'small_logo' => 'img/small-logos/vp360.png',
                'color' => '#1a1a1a',
                'telefono' => '1234567890',
                'correo' => 'contacto@vp360.com',
                'descripcion_adicional' => 'VP360 es una empresa de tecnología que se dedica a la innovación y desarrollo de soluciones tecnológicas para empresas y emprendedores.'
                    . PHP_EOL . PHP_EOL . 'Nuestro objetivo es brindar soluciones tecnológicas que permitan a nuestros clientes mejorar sus procesos y aumentar su productividad.',
            ],
            [
                'nombre' => 'Tech Solutions',
                'descripcion' => 'Soluciones Tecnológicas - Innovación y Desarrollo',
                'logo' => 'img/logos/tech-solutions.png',
                'small_logo' => 'img/small-logos/tech-solutions.png',
                'color' => '#1a1a1a',
                'telefono' => '1234567890',
                'correo' => 'contacto@tech.com',
                'descripcion_adicional' => 'Tech Solutions es una empresa de tecnología que se dedica a la innovación y desarrollo de soluciones tecnológicas para empresas y emprendedores.'
                    . PHP_EOL . PHP_EOL . 'Nuestro objetivo es brindar soluciones tecnológicas que permitan a nuestros clientes mejorar sus procesos y aumentar su productividad.',
            ]
        ];

        $nombres = array_column($administradores, 'nombre');

        foreach ($administradores as $administrador) {
            Administrador::updateOrCreate(
                ['nombre' => $administrador['nombre']],
                $administrador
            );
        }

        Administrador::whereNotIn('nombre', $nombres)->delete();
    }
}
