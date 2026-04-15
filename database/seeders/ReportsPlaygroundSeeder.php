<?php

namespace Database\Seeders;

use App\Models\Contacto;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\Zona;
use App\Models\ZonaSection;
use Database\Seeders\Concerns\SeedsMediaAssets;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class ReportsPlaygroundSeeder extends Seeder
{
    use SeedsMediaAssets;

    public function run()
    {
        $defaultPropertyImages = $this->mergeSeedImagePools(
            $this->existingPublicAssets('properties'),
            $this->existingPublicAssets('propiedad/storage/properties'),
            $this->publishSeedAssets('properties/defaults', 'properties/defaults')
        );
        $playgroundPropertyImages = $this->mergeSeedImagePools(
            $this->existingPublicAssets('properties/playground'),
            $this->publishSeedAssets('properties/playground', 'properties/playground'),
            $defaultPropertyImages
        );
        $zoneHeaderImages = $this->existingPublicAssets('zonas');
        $zoneSectionImages = $this->existingPublicAssets('zonas/secciones');
        $zones = $this->seedZones($zoneHeaderImages, $zoneSectionImages);
        $properties = $this->seedProperties($zones, $defaultPropertyImages, $playgroundPropertyImages);

        $this->seedContacts($properties);
    }

    protected function seedZones(array $zoneHeaderImages, array $zoneSectionImages): array
    {
        $definitions = [
            [
                'nombre' => 'Centro',
                'sections' => [
                    ['titulo' => 'Vida urbana', 'descripcion' => 'Servicios, restauracion y vida a pie de calle.'],
                    ['titulo' => 'Conexion', 'descripcion' => 'Buena movilidad para clientes que priorizan ubicacion.'],
                ],
            ],
            [
                'nombre' => 'Costa',
                'sections' => [
                    ['titulo' => 'Frente al mar', 'descripcion' => 'Producto vacacional y segunda residencia de ticket alto.'],
                    ['titulo' => 'Estilo de vida', 'descripcion' => 'Piscina, terraza y demanda internacional.'],
                ],
            ],
            [
                'nombre' => 'Residencial Norte',
                'sections' => [
                    ['titulo' => 'Familiar', 'descripcion' => 'Chalets, adosados y demanda de primera vivienda.'],
                    ['titulo' => 'Crecimiento', 'descripcion' => 'Zona util para captar familias y compradores estables.'],
                ],
            ],
            [
                'nombre' => 'Campo',
                'sections' => [
                    ['titulo' => 'Privacidad', 'descripcion' => 'Fincas y villas con terreno.'],
                ],
            ],
        ];

        $zones = [];

        foreach ($definitions as $zoneIndex => $definition) {
            $zone = Zona::updateOrCreate(
                ['nombre' => $definition['nombre']],
                ['imagen_principal' => $this->cycleSeedImage($zoneHeaderImages, $zoneIndex)]
            );

            foreach ($definition['sections'] as $sectionIndex => $section) {
                ZonaSection::updateOrCreate(
                    [
                        'zona_id' => $zone->id,
                        'titulo' => $section['titulo'],
                    ],
                    [
                        'imagen' => $this->cycleSeedImage($zoneSectionImages, ($zoneIndex * 3) + $sectionIndex),
                        'descripcion' => $section['descripcion'],
                    ]
                );
            }

            $zones[$definition['nombre']] = $zone;
        }

        return $zones;
    }

    protected function seedProperties(array $zones, array $defaultPropertyImages, array $playgroundPropertyImages): array
    {
        $definitions = [
            [
                'ref' => 'PG-001',
                'title' => 'Atico boutique en Centro',
                'location' => 'Centro historico',
                'price' => 395000,
                'tipo' => 'piso',
                'status' => 'published',
                'is_featured' => true,
                'thumbnail' => 'properties/playground/atico-centro.jpg',
                'zona' => 'Centro',
                'bedrooms' => 2,
                'bathrooms' => 2,
                'area' => 118,
                'images' => 4,
            ],
            [
                'ref' => 'PG-002',
                'title' => 'Piso reformado junto al paseo',
                'location' => 'Centro comercial',
                'price' => 278000,
                'tipo' => 'piso',
                'status' => 'published',
                'is_featured' => false,
                'thumbnail' => 'properties/playground/piso-paseo.jpg',
                'zona' => 'Centro',
                'bedrooms' => 3,
                'bathrooms' => 2,
                'area' => 102,
                'images' => 3,
            ],
            [
                'ref' => 'PG-003',
                'title' => 'Villa panoramica en Costa',
                'location' => 'Primera linea',
                'price' => 1250000,
                'tipo' => 'villa',
                'status' => 'published',
                'is_featured' => true,
                'thumbnail' => 'properties/playground/villa-costa.jpg',
                'zona' => 'Costa',
                'bedrooms' => 5,
                'bathrooms' => 4,
                'area' => 342,
                'images' => 5,
            ],
            [
                'ref' => 'PG-004',
                'title' => 'Casa mediterranea con piscina',
                'location' => 'Costa sur',
                'price' => 690000,
                'tipo' => 'casa',
                'status' => 'published',
                'is_featured' => true,
                'thumbnail' => 'properties/playground/casa-med.jpg',
                'zona' => 'Costa',
                'bedrooms' => 4,
                'bathrooms' => 3,
                'area' => 210,
                'images' => 4,
            ],
            [
                'ref' => 'PG-005',
                'title' => 'Chalet familiar en Residencial Norte',
                'location' => 'Residencial Norte',
                'price' => 485000,
                'tipo' => 'casa',
                'status' => 'draft',
                'is_featured' => false,
                'thumbnail' => 'properties/playground/chalet-norte.jpg',
                'zona' => 'Residencial Norte',
                'bedrooms' => 4,
                'bathrooms' => 3,
                'area' => 228,
                'images' => 4,
            ],
            [
                'ref' => 'PG-006',
                'title' => 'Adosado listo para entrar',
                'location' => 'Residencial Norte',
                'price' => 318000,
                'tipo' => 'casa',
                'status' => 'published',
                'is_featured' => false,
                'thumbnail' => '',
                'zona' => 'Residencial Norte',
                'bedrooms' => 3,
                'bathrooms' => 2,
                'area' => 144,
                'images' => 2,
            ],
            [
                'ref' => 'PG-007',
                'title' => 'Finca con terreno en Campo',
                'location' => 'Camino viejo',
                'price' => 560000,
                'tipo' => 'villa',
                'status' => 'draft',
                'is_featured' => false,
                'thumbnail' => '',
                'zona' => 'Campo',
                'bedrooms' => 4,
                'bathrooms' => 2,
                'area' => 260,
                'images' => 3,
            ],
            [
                'ref' => 'PG-008',
                'title' => 'Casa de campo para escapadas',
                'location' => 'Campo alto',
                'price' => 340000,
                'tipo' => 'casa',
                'status' => 'published',
                'is_featured' => false,
                'thumbnail' => 'properties/playground/campo-escapadas.jpg',
                'zona' => 'Campo',
                'bedrooms' => 3,
                'bathrooms' => 2,
                'area' => 172,
                'images' => 3,
            ],
        ];

        $properties = [];

        foreach ($definitions as $definition) {
            $galleryPool = $playgroundPropertyImages !== [] ? $playgroundPropertyImages : $defaultPropertyImages;
            $fallbackImage = $this->cycleSeedImage($galleryPool, count($properties));
            $preferredThumbnail = in_array($definition['thumbnail'], $playgroundPropertyImages, true)
                ? $definition['thumbnail']
                : $fallbackImage;

            $property = Property::updateOrCreate(
                ['title' => $definition['title']],
                [
                    'ref' => $definition['ref'],
                    'title' => $definition['title'],
                    'location' => $definition['location'],
                    'price' => $definition['price'],
                    'tipo' => $definition['tipo'],
                    'status' => $definition['status'],
                    'is_featured' => $definition['is_featured'],
                    'thumbnail' => $preferredThumbnail,
                    'description' => 'Propiedad de playground para explotar informes, filtros y cuadros comerciales.',
                    'zona_id' => $zones[$definition['zona']]->id,
                    'bedrooms' => $definition['bedrooms'],
                    'bathrooms' => $definition['bathrooms'],
                    'area' => $definition['area'],
                    'tiene_solar' => in_array($definition['tipo'], ['casa', 'villa'], true),
                    'metros_solar' => in_array($definition['tipo'], ['casa', 'villa'], true) ? $definition['area'] * 1.7 : null,
                    'tiene_patio' => in_array($definition['tipo'], ['casa', 'villa'], true),
                    'tiene_piscina' => $definition['tipo'] === 'villa',
                ]
            );

            $property->forceFill(['ref' => $definition['ref']])->saveQuietly();

            if (Schema::hasTable('property_images')) {
                for ($imageIndex = 1; $imageIndex <= $definition['images']; $imageIndex++) {
                    $galleryImage = $this->cycleSeedImage($galleryPool, count($properties) + $imageIndex - 1);

                    if (! $galleryImage) {
                        continue;
                    }

                    PropertyImage::updateOrCreate(
                        [
                            'property_id' => $property->id,
                            'path' => $galleryImage,
                        ],
                        [
                            'url' => '',
                        ]
                    );
                }
            }

            $properties[$definition['ref']] = $property;
        }

        return $properties;
    }

    protected function seedContacts(array $properties): void
    {
        $definitions = [
            ['property_ref' => 'PG-001', 'name' => 'Paula Romero', 'email' => 'paula.romero.playground@example.com', 'status' => 'cerrado', 'months_ago' => 5, 'notes' => 'Operacion cerrada tras segunda visita.'],
            ['property_ref' => 'PG-001', 'name' => 'Javier Molina', 'email' => 'javier.molina.playground@example.com', 'status' => 'contactado', 'months_ago' => 3, 'notes' => 'Pendiente de confirmar financiacion.'],
            ['property_ref' => 'PG-002', 'name' => 'Marta Diaz', 'email' => 'marta.diaz.playground@example.com', 'status' => 'pendiente', 'months_ago' => 2, 'notes' => 'Primer contacto desde formulario.'],
            ['property_ref' => 'PG-002', 'name' => 'Luis Vargas', 'email' => 'luis.vargas.playground@example.com', 'status' => 'contactado', 'months_ago' => 1, 'notes' => 'Solicita visita para el fin de semana.'],
            ['property_ref' => 'PG-003', 'name' => 'Sophie Martin', 'email' => 'sophie.martin.playground@example.com', 'status' => 'cerrado', 'months_ago' => 4, 'notes' => 'Cliente internacional, compra avanzada.'],
            ['property_ref' => 'PG-003', 'name' => 'Marcus Reed', 'email' => 'marcus.reed.playground@example.com', 'status' => 'contactado', 'months_ago' => 2, 'notes' => 'Enviada documentacion tecnica.'],
            ['property_ref' => 'PG-003', 'name' => 'Elena Costa', 'email' => 'elena.costa.playground@example.com', 'status' => 'pendiente', 'months_ago' => 0, 'notes' => 'Lead caliente por recomendacion.'],
            ['property_ref' => 'PG-004', 'name' => 'Nicolas Perez', 'email' => 'nicolas.perez.playground@example.com', 'status' => 'contactado', 'months_ago' => 1, 'notes' => 'Quiere negociar tiempos de entrega.'],
            ['property_ref' => 'PG-004', 'name' => 'Lucia Cano', 'email' => 'lucia.cano.playground@example.com', 'status' => 'pendiente', 'months_ago' => 0, 'notes' => 'Pide mas fotos exteriores.'],
            ['property_ref' => 'PG-005', 'name' => 'Irene Lopez', 'email' => 'irene.lopez.playground@example.com', 'status' => 'pendiente', 'months_ago' => 1, 'notes' => 'Interesada aunque la ficha aun esta en borrador.'],
            ['property_ref' => 'PG-006', 'name' => 'Carlos Serra', 'email' => 'carlos.serra.playground@example.com', 'status' => 'contactado', 'months_ago' => 2, 'notes' => 'Esperando decision de pareja.'],
            ['property_ref' => 'PG-006', 'name' => 'Beatriz Navas', 'email' => 'beatriz.navas.playground@example.com', 'status' => 'pendiente', 'months_ago' => 0, 'notes' => 'Solicita financiacion orientativa.'],
            ['property_ref' => 'PG-007', 'name' => 'Miguel Torres', 'email' => 'miguel.torres.playground@example.com', 'status' => 'pendiente', 'months_ago' => 3, 'notes' => 'Busca terreno amplio y privacidad.'],
            ['property_ref' => 'PG-008', 'name' => 'Andrea Rios', 'email' => 'andrea.rios.playground@example.com', 'status' => 'cerrado', 'months_ago' => 2, 'notes' => 'Reserva firmada.'],
            ['property_ref' => 'PG-008', 'name' => 'Daniel Soler', 'email' => 'daniel.soler.playground@example.com', 'status' => 'contactado', 'months_ago' => 0, 'notes' => 'Quiere visita virtual.'],
        ];

        foreach ($definitions as $index => $definition) {
            $property = $properties[$definition['property_ref']];
            $createdAt = Carbon::now()->subMonths($definition['months_ago'])->subDays(($index % 4) * 3)->setTime(10 + ($index % 7), 0);

            $payload = [
                'nombre' => $definition['name'],
                'telefono' => '600' . str_pad((string) ($index + 111111), 6, '0', STR_PAD_LEFT),
                'mensaje' => 'Consulta de playground para informes sobre ' . $property->title . '.',
                'status' => $definition['status'],
                'property_id' => $property->id,
                'internal_notes' => $definition['notes'],
                'last_contacted_at' => in_array($definition['status'], ['contactado', 'cerrado'], true) ? $createdAt->copy()->addDays(2) : null,
                'next_action_at' => match ($definition['status']) {
                    'pendiente' => $createdAt->copy()->addDays(5)->toDateString(),
                    'contactado' => $createdAt->copy()->addDays(10)->toDateString(),
                    default => null,
                },
            ];

            Contacto::updateOrCreate(
                [
                    'email' => $definition['email'],
                    'property_id' => $property->id,
                ],
                array_merge($payload, [
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt->copy()->addDays(2),
                ])
            );
        }

        Contacto::updateOrCreate(
            ['email' => 'lead.sin.propiedad.playground@example.com'],
            [
                'nombre' => 'Lead General',
                'telefono' => '600999999',
                'mensaje' => 'Consulta general sin propiedad asociada.',
                'status' => 'pendiente',
                'property_id' => null,
                'internal_notes' => 'Lead de cabecera para probar informes y bandeja general.',
                'next_action_at' => now()->addDay()->toDateString(),
                'created_at' => now()->subWeeks(2),
                'updated_at' => now()->subWeeks(2),
            ]
        );
    }
}
