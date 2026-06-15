<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\PropertyImage;
use Database\Seeders\Concerns\SeedsMediaAssets;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PropertySeeder extends Seeder
{
    use SeedsMediaAssets;

    public function run()
    {
        $publishedAssets = $this->publishResourceAssets('assets', 'properties');
        $groupedAssets = $this->groupAssetsByListing($publishedAssets);
        $definitions = $this->buildDefinitionsFromAssets($groupedAssets);
        $seedRefs = [];

        foreach ($definitions as $definition) {
            $seedRefs[] = $definition['ref'];

            $property = Property::updateOrCreate(
                ['ref' => $definition['ref']],
                [
                    'title' => $definition['title'],
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
                    'thumbnail' => $definition['gallery'][0] ?? null,
                ]
            );

            $property->forceFill([
                'ref' => $definition['ref'],
                'thumbnail' => $definition['gallery'][0] ?? null,
            ])->saveQuietly();

            $galleryPaths = $definition['gallery'];

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

        Property::query()
            ->where('ref', 'like', 'ASSET-%')
            ->whereNotIn('ref', $seedRefs)
            ->each(function (Property $property) {
                $property->images()->delete();
                $property->delete();
            });
    }

    protected function groupAssetsByListing(array $publishedAssets): array
    {
        return collect($publishedAssets)
            ->groupBy(function (string $assetPath) {
                $filename = pathinfo($assetPath, PATHINFO_FILENAME);
                $parts = explode('-', $filename, 2);

                return $parts[0];
            })
            ->map(function ($paths, string $groupKey) {
                return collect($paths)
                    ->sortBy(function (string $path) use ($groupKey) {
                        $filename = pathinfo($path, PATHINFO_FILENAME);

                        return $filename === $groupKey ? '0-' . $filename : '1-' . $filename;
                    })
                    ->values()
                    ->all();
            })
            ->sortKeys()
            ->values()
            ->all();
    }

    protected function buildDefinitionsFromAssets(array $groupedAssets): array
    {
        $priceBaseByType = [
            'piso' => 235000,
            'casa' => 410000,
            'villa' => 890000,
            'terreno' => 175000,
        ];

        return collect($groupedAssets)->values()->map(function (array $gallery, int $index) use ($priceBaseByType) {
            $assetPath = $gallery[0];
            $filename = pathinfo($assetPath, PATHINFO_FILENAME);
            $baseName = preg_replace('/[\W_]*\d+$/', '', $filename) ?: $filename;
            $type = $this->resolvePropertyTypeFromAssetName($baseName);
            $profile = $this->propertyProfileForAsset($filename, $type, $index);
            $title = $profile['title'];
            $location = $profile['location'];
            $ref = 'ASSET-' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT);

            return [
                'ref' => $ref,
                'title' => $title,
                'location' => $location,
                'price' => $profile['price'] ?? (($priceBaseByType[$type] ?? 295000) + ($index * 35000)),
                'tipo' => $type,
                'status' => $profile['status'] ?? ($type === 'terreno' ? 'draft' : 'published'),
                'is_featured' => $profile['is_featured'] ?? ($index < 2),
                'description' => $profile['description'] ?? $this->buildDescription($type, $location, $filename),
                'bedrooms' => $profile['bedrooms'] ?? $this->bedroomsForType($type),
                'bathrooms' => $profile['bathrooms'] ?? $this->bathroomsForType($type),
                'area' => $profile['area'] ?? $this->areaForType($type, $index),
                'tiene_solar' => in_array($type, ['casa', 'villa', 'terreno'], true),
                'metros_solar' => $profile['metros_solar'] ?? $this->plotAreaForType($type, $index),
                'tiene_patio' => in_array($type, ['casa', 'villa'], true),
                'tiene_piscina' => $profile['tiene_piscina'] ?? ($type === 'villa'),
                'quick_summary_1' => $profile['quick_summary_1'] ?? $this->summaryOne($type, $filename),
                'quick_summary_2' => $profile['quick_summary_2'] ?? $this->summaryTwo($type, $index),
                'quick_summary_3' => $profile['quick_summary_3'] ?? $this->summaryThree($type),
                'gallery' => $gallery,
            ];
        })->all();
    }

    protected function resolvePropertyTypeFromAssetName(string $baseName): string
    {
        $normalized = Str::lower(Str::ascii($baseName));

        return match (true) {
            str_contains($normalized, 'terreno'),
            str_contains($normalized, 'solar'),
            str_contains($normalized, 'parcela') => 'terreno',
            str_contains($normalized, 'villa') => 'villa',
            str_contains($normalized, 'chalet'),
            str_contains($normalized, 'casa') => 'casa',
            default => 'piso',
        };
    }

    protected function propertyProfileForAsset(string $filename, string $type, int $index): array
    {
        $profiles = [
            'piso1' => [
                'title' => 'Apartamento Mirador del Bulevar',
                'location' => 'Centro urbano de Alicante',
                'price' => 248000,
                'status' => 'published',
                'is_featured' => true,
                'bedrooms' => 2,
                'bathrooms' => 2,
                'area' => 98,
                'description' => 'Piso en ciudad con una imagen limpia y actual, pensado para cliente que quiere vivir cerca de comercios, restauración y vida urbana.',
                'quick_summary_1' => 'Piso de perfil urbano con mucha luz, dos dormitorios y una implantación muy cómoda para el día a día.',
                'quick_summary_2' => '98 m2 construidos en pleno entorno de ciudad, ideal para primera residencia o inversión patrimonial.',
                'quick_summary_3' => 'Una oportunidad muy redonda para comprador que prioriza ubicación, practicidad y una vivienda lista para entrar.',
            ],
            'piso2' => [
                'title' => 'Piso Alameda de la Estación',
                'location' => 'Zona centro de Elche',
                'price' => 269000,
                'status' => 'published',
                'is_featured' => false,
                'bedrooms' => 3,
                'bathrooms' => 2,
                'area' => 112,
                'description' => 'Vivienda en ciudad con distribución familiar y buena conexión con servicios, transporte y zonas comerciales.',
                'quick_summary_1' => 'Piso de tres dormitorios con una lectura funcional y muy apetecible para familia urbana.',
                'quick_summary_2' => '112 m2 construidos con una base muy versátil para residencia habitual o compra reposición.',
                'quick_summary_3' => 'Encaja muy bien con cliente que busca metros, buena ubicación y margen para entrar sin grandes reformas.',
            ],
            'piso3' => [
                'title' => 'Residencial Plaza Nova',
                'location' => 'Centro de Valencia',
                'price' => 315000,
                'status' => 'published',
                'is_featured' => true,
                'bedrooms' => 3,
                'bathrooms' => 2,
                'area' => 124,
                'description' => 'Piso de ciudad con presencia más representativa, pensado para cliente que quiere ubicación consolidada y una imagen cuidada.',
                'quick_summary_1' => 'Vivienda urbana con tres dormitorios y una presencia muy sólida para cliente final o inversión de calidad.',
                'quick_summary_2' => '124 m2 construidos en una zona muy viva de ciudad, con comercios y servicios a mano.',
                'quick_summary_3' => 'Perfecto para comprador que quiere centralidad, amplitud y una propiedad con recorrido comercial rápido.',
            ],
            'chalet1' => [
                'title' => 'Chalet La Pinada',
                'location' => 'Entorno rural de Pedreguer',
                'price' => 465000,
                'status' => 'published',
                'is_featured' => false,
                'bedrooms' => 4,
                'bathrooms' => 2,
                'area' => 186,
                'metros_solar' => 780,
                'description' => 'Chalet en el campo con buena parcela y una lectura muy familiar, ideal para quien quiere tranquilidad sin alejarse demasiado del núcleo urbano.',
                'quick_summary_1' => 'Chalet rodeado de campo con una implantación cómoda para familia y vida exterior.',
                'quick_summary_2' => '186 m2 construidos sobre 780 m2 de parcela, con espacio para jardín, descanso y uso cotidiano.',
                'quick_summary_3' => 'Muy buena opción para cliente que busca privacidad, amplitud y una casa para disfrutar todo el año.',
            ],
            'chalet2' => [
                'title' => 'Finca Los Almendros',
                'location' => 'Campo de Benissa',
                'price' => 529000,
                'status' => 'published',
                'is_featured' => false,
                'bedrooms' => 4,
                'bathrooms' => 3,
                'area' => 214,
                'metros_solar' => 960,
                'description' => 'Vivienda de campo con carácter y buen apoyo exterior, pensada para comprador que valora privacidad y una parcela útil.',
                'quick_summary_1' => 'Casa de campo con presencia muy equilibrada y una parcela que acompaña bien el estilo de vida exterior.',
                'quick_summary_2' => '214 m2 construidos y 960 m2 de terreno para reforzar la sensación de amplitud y desahogo.',
                'quick_summary_3' => 'Una compra muy convincente para quien quiere salir de la ciudad y ganar calidad de vida sin renunciar a comodidad.',
            ],
            'chalet3' => [
                'title' => 'Villa Serra Verda',
                'location' => 'Partida rural de Jávea',
                'price' => 685000,
                'status' => 'published',
                'is_featured' => true,
                'bedrooms' => 4,
                'bathrooms' => 3,
                'area' => 248,
                'metros_solar' => 1320,
                'tiene_piscina' => true,
                'description' => 'Chalet de campo más completo visualmente, con fotos de salón, baño y dormitorio para construir una ficha mucho más rica y creíble.',
                'quick_summary_1' => 'Villa en el campo con recorrido fotográfico completo entre exterior, salón, dormitorio y baño.',
                'quick_summary_2' => '248 m2 construidos sobre 1.320 m2 de parcela, con piscina y una lectura claramente orientada a cliente familiar-premium.',
                'quick_summary_3' => 'Una propiedad muy comercial para visitas cualificadas, segunda residencia de nivel o comprador que quiere espacio y vida exterior.',
            ],
            'terreno' => [
                'title' => 'Parcela El Olivar',
                'location' => 'Suelo rústico en las afueras de Benigembla',
                'price' => 189000,
                'status' => 'draft',
                'is_featured' => false,
                'bedrooms' => null,
                'bathrooms' => null,
                'area' => 0,
                'metros_solar' => 1840,
                'tiene_piscina' => false,
                'description' => 'Terreno en el campo con buena lectura para captación o proyecto futuro, orientado a cliente que busca amplitud y entorno natural.',
                'quick_summary_1' => 'Parcela en entorno rural con buen potencial para proyecto residencial o patrimonial a medio plazo.',
                'quick_summary_2' => '1.840 m2 de suelo para trabajar una captación diferencial en zona de campo.',
                'quick_summary_3' => 'Interesante para comprador que quiere espacio, vistas abiertas y una operación con visión de futuro.',
            ],
        ];

        return $profiles[$filename] ?? [
            'title' => $this->fallbackPropertyTitle($type, $index),
            'location' => in_array($type, ['casa', 'villa', 'terreno'], true) ? 'Entorno rural de interior' : 'Zona urbana consolidada',
        ];
    }

    protected function fallbackPropertyTitle(string $type, int $index): string
    {
        $titles = [
            'piso' => [
                'Apartamento con luz en zona urbana',
                'Piso de ciudad con distribución cuidada',
                'Vivienda urbana lista para entrar',
            ],
            'casa' => [
                'Chalet de campo con parcela',
                'Casa rural con buena implantación exterior',
                'Vivienda independiente en entorno natural',
            ],
            'villa' => [
                'Villa con parcela amplia y vida exterior',
                'Villa residencial de perfil familiar',
                'Propiedad de campo con aspiración premium',
            ],
            'terreno' => [
                'Parcela rústica con potencial',
                'Suelo de campo para nuevo proyecto',
            ],
        ];

        $pool = $titles[$type] ?? ['Propiedad destacada del catálogo'];

        return $pool[$index % count($pool)];
    }

    protected function buildDescription(string $type, string $location, string $filename): string
    {
        return match ($type) {
            'villa' => 'Villa generada a partir del asset ' . $filename . ', con posicionamiento premium y foco en exterior en ' . $location . '.',
            'casa' => 'Casa residencial creada desde el asset ' . $filename . ' para poblar el catálogo con una propiedad independiente en ' . $location . '.',
            'terreno' => 'Terreno cargado desde el asset ' . $filename . ' para probar fichas de suelo y captación dentro del catálogo en ' . $location . '.',
            default => 'Piso creado automáticamente desde el asset ' . $filename . ' como propiedad individual en ' . $location . '.',
        };
    }

    protected function bedroomsForType(string $type): ?int
    {
        return match ($type) {
            'villa' => 5,
            'casa' => 4,
            'terreno' => null,
            default => 3,
        };
    }

    protected function bathroomsForType(string $type): ?int
    {
        return match ($type) {
            'villa' => 4,
            'casa' => 3,
            'terreno' => null,
            default => 2,
        };
    }

    protected function areaForType(string $type, int $index): int
    {
        return match ($type) {
            'villa' => 300 + ($index * 12),
            'casa' => 180 + ($index * 10),
            'terreno' => 0,
            default => 95 + ($index * 8),
        };
    }

    protected function plotAreaForType(string $type, int $index): ?int
    {
        return match ($type) {
            'villa' => 900 + ($index * 60),
            'casa' => 520 + ($index * 35),
            'terreno' => 1100 + ($index * 90),
            default => null,
        };
    }

    protected function summaryOne(string $type, string $filename): string
    {
        return match ($type) {
            'villa' => 'Villa de ticket alto sembrada desde ' . $filename . ' como ficha individual.',
            'casa' => 'Casa residencial construida a partir del asset ' . $filename . '.',
            'terreno' => 'Suelo sembrado desde ' . $filename . ' para validar el flujo de inmuebles sin estancias interiores.',
            default => 'Piso creado desde ' . $filename . ' como propiedad independiente.',
        };
    }

    protected function summaryTwo(string $type, int $index): string
    {
        return match ($type) {
            'villa' => (300 + ($index * 12)) . ' m2 construidos y una lectura claramente premium para demos comerciales.',
            'casa' => (180 + ($index * 10)) . ' m2 construidos con patio y configuración pensada para familia.',
            'terreno' => (1100 + ($index * 90)) . ' m2 de parcela para pruebas de captación, filtros y fichas de suelo.',
            default => (95 + ($index * 8)) . ' m2 construidos con foco en luz, funcionalidad y producto de demanda alta.',
        };
    }

    protected function summaryThree(string $type): string
    {
        return match ($type) {
            'terreno' => 'Operación enfocada a captación de suelo o a cliente con tiempo para madurar un proyecto en una zona de campo atractiva.',
            'casa' => 'Una operación pensada para comprador familiar que valora parcela, tranquilidad y una casa con uso real desde el primer día.',
            'villa' => 'Producto con mucho tiron para segunda residencia de nivel, cambio de estilo de vida o comprador internacional que busca campo y privacidad.',
            default => 'Operación muy comercial para cliente urbano que quiere ubicación, comodidad y una vivienda fácil de defender en visita.',
        };
    }
}
