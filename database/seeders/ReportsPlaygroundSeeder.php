<?php

namespace Database\Seeders;

use App\Models\Contacto;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\Setting;
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

        $zones = $this->seedZones();
        $properties = $this->seedProperties($zones, $defaultPropertyImages, $playgroundPropertyImages);
        $this->seedHeroSettings($zones);

        $this->seedContacts($properties);
    }

    protected function seedZones(): array
    {
        $definitions = [
            [
                'nombre' => 'Centro',
                'header_asset' => 'centro-ciudad',
                'sections' => [
                    [
                        'asset' => 'vida-urbana',
                        'titulo' => 'Vida urbana',
                        'descripcion' => "Una zona pensada para cliente que quiere bajar a la calle y tenerlo todo a mano: cafeterias, comercios, restauracion y ritmo diario.\n\nAqui encajan muy bien los pisos para primera residencia, compradores que vienen de alquiler y perfiles que valoran poder moverse sin depender del coche.\n\nTambien funciona especialmente bien en visita porque la sensacion de barrio, actividad y servicios se percibe enseguida.",
                    ],
                    [
                        'asset' => 'conexion',
                        'titulo' => 'Conexion',
                        'descripcion' => "Centro tiene una lectura muy clara en movilidad: accesos comodos, transporte, conexiones rapidas y una vida diaria facil de organizar.\n\nEs una zona que suele convencer a perfiles profesionales, familias urbanas y compradores que priorizan tiempo, practicidad y cercania.\n\nA nivel comercial, ayuda mucho cuando queremos defender ubicaciones con uso real los doce meses del ano.",
                    ],
                ],
            ],
            [
                'nombre' => 'Costa',
                'header_asset' => 'costa',
                'sections' => [
                    [
                        'asset' => 'frente-al-mar',
                        'titulo' => 'Frente al mar',
                        'descripcion' => "La costa concentra el producto mas aspiracional del catalogo: vistas abiertas, cercania al agua y una sensacion inmediata de escapada premium.\n\nAqui encontramos muy buena respuesta en segunda residencia, cliente internacional y operaciones donde el componente emocional pesa tanto como el racional.\n\nVisualmente es una zona muy potente para portada, destacados y fichas que necesitan impacto desde la primera imagen.",
                    ],
                    [
                        'asset' => 'estilo-de-vida',
                        'titulo' => 'Estilo de vida',
                        'descripcion' => "Terrazas, piscina, luz natural y una forma de vivir mucho mas abierta al exterior definen muy bien este entorno.\n\nSuele funcionar con comprador que busca descanso, teletrabajo con calidad de vida o una vivienda de disfrute familiar en periodos largos.\n\nA nivel comercial, es una zona facil de defender porque mezcla estilo de vida, valor percibido y una demanda muy reconocible.",
                    ],
                ],
            ],
            [
                'nombre' => 'Residencial Norte',
                'header_asset' => 'residencial-norte',
                'sections' => [
                    [
                        'asset' => 'familiar',
                        'titulo' => 'Familiar',
                        'descripcion' => "Residencial Norte tiene una lectura muy clara de estabilidad: chalets, adosados y vivienda pensada para quedarse.\n\nEncaja bien con familias que buscan metros, zonas tranquilas y una rutina mas comoda sin salir del entorno urbano ampliado.\n\nEs una ubicacion muy util para propiedades de reposicion, primera compra consolidada o cambio a una casa con mas espacio.",
                    ],
                    [
                        'asset' => 'crecimiento',
                        'titulo' => 'Crecimiento',
                        'descripcion' => "Es una zona que transmite evolucion y proyecto de vida, algo que comercialmente ayuda mucho cuando hablamos de compradores con vision a medio plazo.\n\nTiene capacidad para captar perfiles estables, familias que comparan bien y clientes que buscan equilibrio entre precio, superficie y entorno.\n\nAdemas, ofrece margen para presentar producto con narrativa de mejora, arraigo y futuro.",
                    ],
                ],
            ],
            [
                'nombre' => 'Campo',
                'header_asset' => 'campo',
                'sections' => [
                    [
                        'asset' => 'privacidad',
                        'titulo' => 'Privacidad',
                        'descripcion' => "El campo conecta con un comprador que busca silencio, vistas abiertas y distancia respecto al ritmo de ciudad.\n\nAqui encajan fincas, villas con terreno y operaciones donde la parcela y el exterior pesan tanto como la vivienda principal.\n\nEs un entorno muy atractivo para segunda residencia, cambio de estilo de vida o cliente que quiere espacio real para disfrutar con calma.",
                    ],
                ],
            ],
        ];

        $headerImages = collect($this->publishResourceAssets('assets/entornos', 'zonas'))
            ->keyBy(fn (string $path) => pathinfo($path, PATHINFO_FILENAME));

        $zones = [];

        foreach ($definitions as $definition) {
            $zone = Zona::updateOrCreate(
                ['nombre' => $definition['nombre']],
                ['imagen_principal' => $headerImages[$definition['header_asset']] ?? null]
            );

            $sectionImages = collect(
                $this->publishResourceAssets(
                    'assets/entornos/' . $definition['header_asset'],
                    'zonas/secciones/' . $definition['header_asset']
                )
            )->keyBy(fn (string $path) => pathinfo($path, PATHINFO_FILENAME));

            foreach ($definition['sections'] as $section) {
                ZonaSection::updateOrCreate(
                    [
                        'zona_id' => $zone->id,
                        'titulo' => $section['titulo'],
                    ],
                    [
                        'imagen' => $sectionImages[$section['asset']] ?? null,
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

    protected function seedHeroSettings(array $zones): void
    {
        $propertyImages = collect($this->existingPublicAssets('properties'))
            ->keyBy(fn (string $path) => pathinfo($path, PATHINFO_FILENAME));

        $zoneImages = collect($zones)
            ->mapWithKeys(fn (Zona $zona, string $key) => [$key => $zona->imagen_principal ? '/storage/' . $zona->imagen_principal : null]);

        $settings = [
            'home_hero_count' => '3',
            'home_hero_image_1' => isset($propertyImages['chalet3']) ? '/storage/' . $propertyImages['chalet3'] : ($zoneImages['Costa'] ?? '/images/our-company.jpg'),
            'home_hero_image_2' => isset($propertyImages['piso3']) ? '/storage/' . $propertyImages['piso3'] : ($zoneImages['Centro'] ?? '/images/images.jpg'),
            'home_hero_image_3' => $zoneImages['Costa'] ?? '/images/images.jpg',
            'about_header_image' => $zoneImages['Campo'] ?? '/images/our-company.jpg',
            'contact_header_image' => $zoneImages['Centro'] ?? '/images/our-company.jpg',
            'environment_header_image' => $zoneImages['Costa'] ?? '/images/images.jpg',
            'register_header_image' => $zoneImages['Residencial Norte'] ?? '/images/our-company.jpg',
        ];

        foreach ($settings as $key => $value) {
            Setting::setValue($key, $value);
        }
    }
}
