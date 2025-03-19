<?php

namespace Database\Seeders;

use App\Models\Proveedor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProveedorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $proveedores = [
            [
                'nombre' => 'Google',
                'descripcion' => 'Motor de búsqueda',
                'logo' => 'img/logos/google.png',
                'small_logo' => 'img/small-logos/google.png',
                'color' => '#1a1a1a',
                'telefono' => '1234567890',
                'correo' => 'contactp@google.com',
                'descripcion_adicional' => 'Google es una empresa de tecnología que se dedica a la innovación y desarrollo de soluciones tecnológicas para empresas y emprendedores.'
                    . PHP_EOL . PHP_EOL . 'Nuestro objetivo es brindar soluciones tecnológicas que permitan a nuestros clientes mejorar sus procesos y aumentar su productividad.',
                'cliente_id' => 1,
            ],
            [
                'nombre' => 'Jira',
                'descripcion' => 'Gestión de proyectos',
                'logo' => 'img/logos/jira.png',
                'small_logo' => 'img/small-logos/jira.png',
                'color' => '#1a1a1a',
                'telefono' => '1234567890',
                'correo' => 'contacto@jira.com',
                'descripcion_adicional' => 'Jira es una empresa de tecnología que se dedica a la innovación y desarrollo de soluciones tecnológicas para empresas y emprendedores.'
                    . PHP_EOL . PHP_EOL . 'Nuestro objetivo es brindar soluciones tecnológicas que permitan a nuestros clientes mejorar sus procesos y aumentar su productividad.',
                'cliente_id' => 1,
            ],
            [
                'nombre' => 'Adobe XD',
                'descripcion' => 'Diseño de interfaces',
                'logo' => 'img/logos/adobe-xd.png',
                'small_logo' => 'img/small-logos/adobe-xd.png',
                'color' => '#1a1a1a',
                'telefono' => '1234567890',
                'correo' => 'contacto@adobe.com',
                'descripcion_adicional' => 'Adobe XD es una empresa de tecnología que se dedica a la innovación y desarrollo de soluciones tecnológicas para empresas y emprendedores.'
                    . PHP_EOL . PHP_EOL . 'Nuestro objetivo es brindar soluciones tecnológicas que permitan a nuestros clientes mejorar sus procesos y aumentar su productividad.',
                'cliente_id' => 2,
            ],
            [
                'nombre' => 'InVision',
                'descripcion' => 'Prototipado',
                'logo' => 'img/logos/invision.png',
                'small_logo' => 'img/small-logos/invision.png',
                'color' => '#1a1a1a',
                'telefono' => '1234567890',
                'correo' => 'contacto@invision.com',
                'descripcion_adicional' => 'InVision es una empresa de tecnología que se dedica a la innovación y desarrollo de soluciones tecnológicas para empresas y emprendedores.'
                    . PHP_EOL . PHP_EOL . 'Nuestro objetivo es brindar soluciones tecnológicas que permitan a nuestros clientes mejorar sus procesos y aumentar su productividad.',
                'cliente_id' => 2,
            ],
            [
                'nombre' => 'Jira',
                'descripcion' => 'Gestión de proyectos',
                'logo' => 'img/logos/jira.png',
                'small_logo' => 'img/small-logos/jira.png',
                'color' => '#1a1a1a',
                'telefono' => '1234567890',
                'correo' => 'contacto@jira.com',
                'descripcion_adicional' => 'Jira es una empresa de tecnología que se dedica a la innovación y desarrollo de soluciones tecnológicas para empresas y emprendedores.'
                    . PHP_EOL . PHP_EOL . 'Nuestro objetivo es brindar soluciones tecnológicas que permitan a nuestros clientes mejorar sus procesos y aumentar su productividad.',
                'cliente_id' => 2,
            ],
            [
                'nombre' => 'Asana',
                'descripcion' => 'Gestión de proyectos',
                'logo' => 'img/logos/asana.png',
                'small_logo' => 'img/small-logos/asana.png',
                'color' => '#1a1a1a',
                'telefono' => '1234567890',
                'correo' => 'contacto@asana.com',
                'descripcion_adicional' => 'Asana es una empresa de tecnología que se dedica a la innovación y desarrollo de soluciones tecnológicas para empresas y emprendedores.'
                    . PHP_EOL . PHP_EOL . 'Nuestro objetivo es brindar soluciones tecnológicas que permitan a nuestros clientes mejorar sus procesos y aumentar su productividad.',
                'cliente_id' => 3,
            ],
            [
                'nombre' => 'Spotify',
                'descripcion' => 'Streaming de música',
                'logo' => 'img/logos/spotify.png',
                'small_logo' => 'img/small-logos/spotify.png',
                'color' => '#1a1a1a',
                'telefono' => '1234567890',
                'correo' => 'contacto@spotify.com',
                'descripcion_adicional' => 'Spotify es una empresa de tecnología que se dedica a la innovación y desarrollo de soluciones tecnológicas para empresas y emprendedores.'
                    . PHP_EOL . PHP_EOL . 'Nuestro objetivo es brindar soluciones tecnológicas que permitan a nuestros clientes mejorar sus procesos y aumentar su productividad.',
                'cliente_id' => 4,
            ],
        ];

        $nombres = array_column($proveedores, 'nombre');

        foreach ($proveedores as $proveedor) {
            Proveedor::updateOrCreate(
                ['nombre' => $proveedor['nombre']],
                $proveedor
            );
        }

        Proveedor::whereNotIn('nombre', $nombres)->delete();
    }
}
