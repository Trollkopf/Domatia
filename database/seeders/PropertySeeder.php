<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\PropertyImage;
use Database\Seeders\Concerns\SeedsMediaAssets;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    use SeedsMediaAssets;

    public function run()
    {
        $propertyImages = $this->mergeSeedImagePools(
            $this->existingPublicAssets('properties'),
            $this->existingPublicAssets('propiedad/storage/properties'),
            $this->publishSeedAssets('properties/defaults', 'properties/defaults')
        );

        $definitions = [
            [
                'ref' => 'SEED-001',
                'title' => 'Villa luminosa con piscina privada',
                'location' => 'Altea',
                'price' => 925000,
                'tipo' => 'villa',
                'status' => 'published',
                'is_featured' => true,
                'bedrooms' => 4,
                'bathrooms' => 3,
                'area' => 285,
                'tiene_solar' => true,
                'metros_solar' => 920,
                'tiene_patio' => true,
                'tiene_piscina' => true,
                'description' => 'Villa pensada para disfrutar de la vida exterior, con piscina privada, zonas de descanso y una distribucion amplia para familias o segunda residencia premium.',
                'quick_summary_1' => 'Villa premium con piscina privada, 4 dormitorios y una implantacion pensada para disfrutar del exterior.',
                'quick_summary_2' => '285 m2 construidos sobre una parcela de 920 m2 con jardin, patio y zonas de estancia amplias.',
                'quick_summary_3' => 'Producto destacado del catalogo, ideal para visitas de cliente final o presentaciones de captacion.',
                'gallery_size' => 5,
            ],
            [
                'ref' => 'SEED-002',
                'title' => 'Piso contemporaneo cerca del mar',
                'location' => 'Calpe',
                'price' => 389000,
                'tipo' => 'piso',
                'status' => 'published',
                'is_featured' => true,
                'bedrooms' => 3,
                'bathrooms' => 2,
                'area' => 118,
                'tiene_solar' => false,
                'metros_solar' => null,
                'tiene_patio' => true,
                'tiene_piscina' => false,
                'description' => 'Piso actualizado con una linea limpia, ideal para cliente que busca cercania a servicios, buena luz natural y una vivienda lista para entrar.',
                'quick_summary_1' => null,
                'quick_summary_2' => null,
                'quick_summary_3' => null,
                'gallery_size' => 4,
            ],
            [
                'ref' => 'SEED-003',
                'title' => 'Casa mediterranea con jardin consolidado',
                'location' => 'Javea',
                'price' => 615000,
                'tipo' => 'casa',
                'status' => 'published',
                'is_featured' => false,
                'bedrooms' => 4,
                'bathrooms' => 3,
                'area' => 214,
                'tiene_solar' => true,
                'metros_solar' => 640,
                'tiene_patio' => true,
                'tiene_piscina' => false,
                'description' => 'Una vivienda con caracter mediterraneo y buen espacio exterior, adecuada para compradores que priorizan jardin, privacidad y comodidad diaria.',
                'quick_summary_1' => 'Casa mediterranea con jardin consolidado y una configuracion muy apetecible para vida familiar.',
                'quick_summary_2' => null,
                'quick_summary_3' => null,
                'gallery_size' => 5,
            ],
            [
                'ref' => 'SEED-004',
                'title' => 'Atico con terraza y vistas despejadas',
                'location' => 'Denia',
                'price' => 455000,
                'tipo' => 'piso',
                'status' => 'published',
                'is_featured' => false,
                'bedrooms' => 2,
                'bathrooms' => 2,
                'area' => 102,
                'tiene_solar' => false,
                'metros_solar' => null,
                'tiene_patio' => true,
                'tiene_piscina' => false,
                'description' => 'Atico pensado para quien valora una gran terraza, luz continua y una sensacion de amplitud muy apetecible tanto para vivir como para escapadas.',
                'quick_summary_1' => 'Atico luminoso con una terraza protagonista y una lectura visual muy limpia para cliente urbano o vacacional.',
                'quick_summary_2' => '102 m2 construidos con salida exterior y una distribucion orientada a aprovechar la luz natural.',
                'quick_summary_3' => null,
                'gallery_size' => 4,
            ],
            [
                'ref' => 'SEED-005',
                'title' => 'Chalet familiar en zona residencial tranquila',
                'location' => 'Benissa',
                'price' => 540000,
                'tipo' => 'casa',
                'status' => 'published',
                'is_featured' => false,
                'bedrooms' => 4,
                'bathrooms' => 2,
                'area' => 196,
                'tiene_solar' => true,
                'metros_solar' => 700,
                'tiene_patio' => true,
                'tiene_piscina' => true,
                'description' => 'Una opcion muy equilibrada para familias que buscan superficie util, jardin, piscina y una zona residencial con ambiente estable.',
                'quick_summary_1' => null,
                'quick_summary_2' => null,
                'quick_summary_3' => null,
                'gallery_size' => 5,
            ],
            [
                'ref' => 'SEED-006',
                'title' => 'Villa de lineas modernas y gran parcela',
                'location' => 'Moraira',
                'price' => 1295000,
                'tipo' => 'villa',
                'status' => 'published',
                'is_featured' => true,
                'bedrooms' => 5,
                'bathrooms' => 4,
                'area' => 338,
                'tiene_solar' => true,
                'metros_solar' => 1180,
                'tiene_patio' => true,
                'tiene_piscina' => true,
                'description' => 'Producto de ticket alto con una imagen muy actual, parcela amplia y una presencia ideal para posicionar catalogo premium desde el seed.',
                'quick_summary_1' => 'Villa de lineas modernas con 5 dormitorios, piscina y una presencia claramente orientada a segmento premium.',
                'quick_summary_2' => '338 m2 construidos y 1.180 m2 de parcela para reforzar el posicionamiento de alto valor en el showroom.',
                'quick_summary_3' => 'Perfecta para demos del backoffice, portada y pruebas de destacados o informes comerciales.',
                'gallery_size' => 5,
            ],
            [
                'ref' => 'SEED-007',
                'title' => 'Piso funcional para primera compra',
                'location' => 'Alicante',
                'price' => 239000,
                'tipo' => 'piso',
                'status' => 'published',
                'is_featured' => false,
                'bedrooms' => 2,
                'bathrooms' => 1,
                'area' => 86,
                'tiene_solar' => false,
                'metros_solar' => null,
                'tiene_patio' => false,
                'tiene_piscina' => false,
                'description' => 'Ficha pensada para tener tambien producto de entrada en el catalogo, con una configuracion realista para cliente de primera compra.',
                'quick_summary_1' => null,
                'quick_summary_2' => null,
                'quick_summary_3' => null,
                'gallery_size' => 3,
            ],
            [
                'ref' => 'SEED-008',
                'title' => 'Casa con patio interior y mucho potencial',
                'location' => 'Pedreguer',
                'price' => 312000,
                'tipo' => 'casa',
                'status' => 'draft',
                'is_featured' => false,
                'bedrooms' => 3,
                'bathrooms' => 2,
                'area' => 148,
                'tiene_solar' => true,
                'metros_solar' => 280,
                'tiene_patio' => true,
                'tiene_piscina' => false,
                'description' => 'Propiedad en borrador para probar flujos de backoffice y asegurar que no todo lo sembrado se publica automaticamente.',
                'quick_summary_1' => 'Casa con patio interior y margen de mejora, util para probar el flujo editorial desde administracion.',
                'quick_summary_2' => null,
                'quick_summary_3' => 'Borrador pensado para validar estados, previews internas y cambios manuales del resumen rapido.',
                'gallery_size' => 4,
            ],
        ];

        foreach ($definitions as $propertyIndex => $definition) {
            $galleryPaths = [];

            for ($imageIndex = 0; $imageIndex < $definition['gallery_size']; $imageIndex++) {
                $galleryPath = $this->cycleSeedImage($propertyImages, ($propertyIndex * 3) + $imageIndex);

                if ($galleryPath) {
                    $galleryPaths[] = $galleryPath;
                }
            }

            $galleryPaths = array_values(array_unique($galleryPaths));

            $property = Property::updateOrCreate(
                ['title' => $definition['title']],
                [
                    'ref' => $definition['ref'],
                    'location' => $definition['location'],
                    'price' => $definition['price'],
                    'tipo' => $definition['tipo'],
                    'status' => $definition['status'],
                    'is_featured' => $definition['is_featured'],
                    'description' => $definition['description'],
                    'bedrooms' => $definition['bedrooms'],
                    'bathrooms' => $definition['bathrooms'],
                    'area' => $definition['area'],
                    'tiene_solar' => $definition['tiene_solar'],
                    'metros_solar' => $definition['metros_solar'],
                    'tiene_patio' => $definition['tiene_patio'],
                    'tiene_piscina' => $definition['tiene_piscina'],
                    'quick_summary_1' => $definition['quick_summary_1'],
                    'quick_summary_2' => $definition['quick_summary_2'],
                    'quick_summary_3' => $definition['quick_summary_3'],
                    'thumbnail' => $galleryPaths[0] ?? null,
                ]
            );

            $property->forceFill([
                'ref' => $definition['ref'],
                'thumbnail' => $galleryPaths[0] ?? null,
            ])->saveQuietly();

            if ($galleryPaths === []) {
                $property->images()->delete();
                continue;
            }

            foreach ($galleryPaths as $galleryPath) {
                PropertyImage::updateOrCreate(
                    [
                        'property_id' => $property->id,
                        'path' => $galleryPath,
                    ],
                    [
                        'url' => '',
                    ]
                );
            }

            PropertyImage::query()
                ->where('property_id', $property->id)
                ->whereNotIn('path', $galleryPaths)
                ->delete();
        }
    }
}
