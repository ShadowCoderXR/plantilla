<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientes = [
            [
                'nombre' => 'Tech Solutions',
                'descripcion' => 'Desarrollo de Software',
                'logo' => 'img/logos/tech-solutions.png',
                'small_logo' => 'img/small-logos/tech-solutions.png',
                'color' => '#1a1a1a',
                'telefono' => '1234567890',
                'correo' => 'contacto@tech.com',
                'descripcion_adicional' => 'Tech Solutions es una empresa de tecnología que se dedica a la innovación y desarrollo de soluciones tecnológicas para empresas y emprendedores.'
                    . PHP_EOL . PHP_EOL . 'Nuestro objetivo es brindar soluciones tecnológicas que permitan a nuestros clientes mejorar sus procesos y aumentar su productividad.',
                'administrador_id' => 1,
            ],
            [
                'nombre' => 'Innova Marketing',
                'descripcion' => 'Marketing Digital',
                'logo' => 'img/logos/innova-marketing.png',
                'small_logo' => 'img/small-logos/innova-marketing.png',
                'color' => '#1a1a1a',
                'telefono' => '1234567890',
                'correo' => 'contacto@innova.com',
                'descripcion_adicional' => 'Innova Marketing es una empresa de marketing digital que se dedica a la innovación y desarrollo de estrategias de marketing para empresas y emprendedores.'
                    . PHP_EOL . PHP_EOL . 'Nuestro objetivo es brindar soluciones de marketing digital que permitan a nuestros clientes mejorar sus procesos y aumentar su productividad.',
                'administrador_id' => 1,
            ],
            [
                'nombre' => 'Moda Trend',
                'descripcion' => 'Ropa y Accesorios',
                'logo' => 'img/logos/moda-trend.png',
                'small_logo' => 'img/small-logos/moda-trend.png',
                'color' => '#1a1a1a',
                'telefono' => '1234567890',
                'correo' => 'contacto@moda.com',
                'descripcion_adicional' => 'Moda Trend es una tienda de ropa y accesorios que se dedica a la venta de ropa y accesorios para mujeres y hombres.'
                    . PHP_EOL . PHP_EOL . 'Nuestro objetivo es brindar ropa y accesorios de moda que permitan a nuestros clientes mejorar su estilo y aumentar su productividad.',
                'administrador_id' => 1,
            ],
            [
                'nombre' => 'Finanzas Pro',
                'descripcion' => 'Asesoría Financiera',
                'logo' => 'img/logos/finanzas-pro.png',
                'small_logo' => 'img/small-logos/finanzas-pro.png',
                'color' => '#1a1a1a',
                'telefono' => '1234567890',
                'correo' => 'contacto@finanzas.com',
                'descripcion_adicional' => 'Finanzas Pro es una empresa de asesoría financiera que se dedica a la innovación y desarrollo de estrategias financieras para empresas y emprendedores.'
                    . PHP_EOL . PHP_EOL . 'Nuestro objetivo es brindar soluciones financieras que permitan a nuestros clientes mejorar sus procesos y aumentar su productividad.',
                'administrador_id' => 1,
            ],
        ];

        $nombres = array_column($clientes, 'nombre');

        foreach ($clientes as $cliente) {
            Cliente::updateOrCreate(
                ['nombre' => $cliente['nombre']],
                $cliente
            );
        }

        Cliente::whereNotIn('nombre', $nombres)->delete();
    }
}
