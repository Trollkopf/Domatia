<?php

namespace App\Services;

use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\PropertyImportRun;
use App\Models\User;
use App\Models\Zona;
use App\Support\PropertyFeatureSupport;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use SimpleXMLElement;
use Throwable;

class KyeroImportService
{
    public function import(string $xml, ?User $user = null, ?string $inputName = null): PropertyImportRun
    {
        $run = $this->prepareImport($xml, $user, $inputName);

        while ($run->status !== 'completed' && $run->status !== 'failed') {
            $run = $this->processNextChunk($run, 1);
        }

        return $run;
    }

    public function prepareImport(string $xml, ?User $user = null, ?string $inputName = null, int $maxImagesPerProperty = 12): PropertyImportRun
    {
        $feed = @simplexml_load_string($xml);

        if (! $feed instanceof SimpleXMLElement) {
            throw new RuntimeException('No se ha podido leer el XML de Kyero.');
        }

        $nodes = $this->extractListingNodes($feed);

        if ($nodes === []) {
            throw new RuntimeException('El XML no contiene inmuebles reconocibles para importar.');
        }

        $run = PropertyImportRun::create([
            'user_id' => $user?->id,
            'source_name' => 'kyero',
            'status' => 'queued',
            'input_name' => $inputName,
            'total_properties' => count($nodes),
            'max_images_per_property' => max(1, min($maxImagesPerProperty, 30)),
            'started_at' => now(),
            'notes' => 'Importación preparada. Pendiente de procesar.',
        ]);

        $payloadPath = 'imports/kyero/run-' . $run->id . '.xml';
        Storage::disk('local')->put($payloadPath, $xml);

        $run->update([
            'payload_path' => $payloadPath,
        ]);

        return $run->fresh();
    }

    public function processNextChunk(PropertyImportRun $run, int $propertiesPerChunk = 1): PropertyImportRun
    {
        if (in_array($run->status, ['completed', 'failed'], true)) {
            return $run->fresh();
        }

        try {
            $feed = $this->loadFeedFromRun($run);
            $nodes = $this->extractListingNodes($feed);
            $chunkSize = max(1, $propertiesPerChunk);
            $offset = (int) $run->properties_seen;
            $slice = array_slice($nodes, $offset, $chunkSize);

            if ($slice === []) {
                $run->update([
                    'status' => 'completed',
                    'notes' => 'Importación completada desde el backoffice.',
                    'finished_at' => now(),
                ]);

                return $run->fresh();
            }

            $run->update([
                'status' => 'running',
                'notes' => sprintf(
                    'Procesando propiedades %d-%d de %d.',
                    $offset + 1,
                    min($offset + count($slice), max($run->total_properties, count($nodes))),
                    max($run->total_properties, count($nodes))
                ),
            ]);

            foreach ($slice as $node) {
                $mapped = $this->mapNode($node);

                $stats = $this->processMappedProperty($mapped, $run->max_images_per_property);

                $run->increment('properties_seen');
                $run->increment('properties_created', $stats['created']);
                $run->increment('properties_updated', $stats['updated']);
                $run->increment('properties_skipped', $stats['skipped']);
                $run->increment('images_downloaded', $stats['images_downloaded']);
            }

            $run->refresh();

            if ($run->properties_seen >= max($run->total_properties, count($nodes))) {
                $run->update([
                    'status' => 'completed',
                    'notes' => 'Importación completada desde el backoffice.',
                    'finished_at' => now(),
                ]);
            }
        } catch (Throwable $exception) {
            $run->update([
                'status' => 'failed',
                'notes' => $exception->getMessage(),
                'finished_at' => now(),
            ]);

            throw $exception;
        }

        return $run->fresh();
    }

    public function startBackgroundProcessing(PropertyImportRun $run, int $chunkSize = 1): bool
    {
        if (app()->runningUnitTests()) {
            return false;
        }

        $chunkSize = max(1, $chunkSize);
        $artisan = base_path('artisan');
        $phpBinary = PHP_BINARY ?: 'php';

        if (DIRECTORY_SEPARATOR === '\\') {
            $command = sprintf(
                'start "" /B "%s" "%s" kyero:process-run %d --chunk=%d',
                $phpBinary,
                $artisan,
                $run->id,
                $chunkSize
            );

            @pclose(@popen('cmd /c ' . $command, 'r'));

            return true;
        }

        $command = sprintf(
            '%s %s kyero:process-run %d --chunk=%d > /dev/null 2>&1 &',
            escapeshellarg($phpBinary),
            escapeshellarg($artisan),
            $run->id,
            $chunkSize
        );

        @exec($command);

        return true;
    }

    protected function extractListingNodes(SimpleXMLElement $feed): array
    {
        $candidates = [
            '//property',
            '//properties/property',
            '//listing',
            '//listings/listing',
            '//advert',
            '//adverts/advert',
            '//item',
            '//items/item',
        ];

        foreach ($candidates as $xpath) {
            $matches = $feed->xpath($xpath);

            if (is_array($matches) && $matches !== []) {
                return $matches;
            }
        }

        return [];
    }

    protected function loadFeedFromRun(PropertyImportRun $run): SimpleXMLElement
    {
        if (! $run->payload_path || ! Storage::disk('local')->exists($run->payload_path)) {
            throw new RuntimeException('No se ha encontrado el XML asociado a esta importacion.');
        }

        $feed = @simplexml_load_string(Storage::disk('local')->get($run->payload_path));

        if (! $feed instanceof SimpleXMLElement) {
            throw new RuntimeException('No se ha podido releer el XML de esta importacion.');
        }

        return $feed;
    }

    protected function mapNode(SimpleXMLElement $node): array
    {
        $descriptionByLocale = $this->extractDescriptionsByLocale($node);
        $images = array_values(array_filter(array_unique(array_merge(
            $this->flattenXPathValues($node, [
                'images/image/url',
                'images/image',
                'images/url',
                'image/url',
                'image',
                'photo',
                'photos/photo',
                'pictures/picture',
            ]),
            $this->flattenAttributes($node, ['url'])
        ))));

        $town = $this->firstValue($node, ['town', 'city', 'location', 'place']);
        $region = $this->firstValue($node, ['region', 'province', 'state']);
        $province = $this->firstValue($node, ['province', 'region', 'state']);
        $country = $this->firstValue($node, ['country']);
        $zonaName = $this->firstValue($node, ['zone', 'area', 'district', 'town', 'city']);
        $normalizedType = $this->normalizeType($this->firstValue($node, ['type', 'property_type']));
        $title = $this->firstValue($node, ['title', 'headline', 'name'])
            ?: $this->buildImportedTitle($normalizedType, $town, $zonaName, $province, $country);
        $location = collect([$town, $province ?: $region])->filter()->implode(', ');
        $features = $this->flattenXPathValues($node, ['features/feature', 'feature']);
        $features = PropertyFeatureSupport::normalizeList($features);
        $derivedFlags = PropertyFeatureSupport::inferFlags($features);
        $plotArea = $this->toInteger($this->firstValue($node, [
            'surface_area/plot',
            'plot_area',
            'land',
            'plot',
        ]));
        $hasPool = ($derivedFlags['tiene_piscina'] ?? false)
            || $this->toBoolean($this->firstValue($node, ['pool', 'swimming_pool']));
        $hasPatio = ($derivedFlags['tiene_patio'] ?? false)
            || $this->toBoolean($this->firstValue($node, ['patio', 'terrace']));
        $description = $descriptionByLocale['es'] ?? $descriptionByLocale['en'] ?? reset($descriptionByLocale) ?: null;
        $standardDescriptionLocales = collect(['en', 'fr', 'de', 'ru'])
            ->mapWithKeys(fn (string $locale) => [$locale => $descriptionByLocale[$locale] ?? null])
            ->all();
        $extraDescriptions = collect($descriptionByLocale)
            ->except(['es', 'en', 'fr', 'de', 'ru'])
            ->filter()
            ->all();
        $latitude = $this->toCoordinate($this->firstValue($node, ['location/latitude', 'latitude']));
        $longitude = $this->toCoordinate($this->firstValue($node, ['location/longitude', 'longitude']));

        return [
            'source_listing_id' => $this->firstValue($node, ['id', 'propertyid', 'property_id', 'listing_id', 'reference']) ?: null,
            'ref' => $this->firstValue($node, ['reference', 'ref']),
            'source_date' => $this->firstValue($node, ['date']),
            'title' => $title,
            'description' => $description,
            'description_en' => $standardDescriptionLocales['en'],
            'description_fr' => $standardDescriptionLocales['fr'],
            'description_de' => $standardDescriptionLocales['de'],
            'description_ru' => $standardDescriptionLocales['ru'],
            'description_extra' => $extraDescriptions,
            'location' => $location !== '' ? $location : ($zonaName ?: null),
            'zona_name' => $zonaName,
            'town' => $town,
            'province' => $province,
            'country' => $country,
            'location_detail' => $this->firstValue($node, ['location_detail']),
            'latitude' => $latitude,
            'longitude' => $longitude,
            'price' => $this->toDecimal($this->firstValue($node, ['price', 'price_value'])),
            'currency' => $this->firstValue($node, ['currency']),
            'price_freq' => $this->firstValue($node, ['price_freq']),
            'tipo' => $normalizedType,
            'bathrooms' => $this->toInteger($this->firstValue($node, ['bathrooms', 'baths'])),
            'bedrooms' => $this->toInteger($this->firstValue($node, ['bedrooms', 'beds'])),
            'area' => $this->toInteger($this->firstValue($node, ['surface_area/built', 'surface_area', 'built', 'built_area', 'area'])),
            'tiene_solar' => $plotArea !== null && $plotArea > 0,
            'metros_solar' => $plotArea,
            'tiene_patio' => $hasPatio,
            'tiene_piscina' => $hasPool,
            'part_ownership' => $this->toBoolean($this->firstValue($node, ['part_ownership'])),
            'leasehold' => $this->toBoolean($this->firstValue($node, ['leasehold'])),
            'new_build' => $this->toBoolean($this->firstValue($node, ['new_build'])),
            'energy_consumption' => $this->firstValue($node, ['energy_rating/consumption', 'consumption']),
            'energy_emissions' => $this->firstValue($node, ['energy_rating/emissions', 'emissions']),
            'video_url' => $this->firstValue($node, ['video_url']),
            'virtual_tour_url' => $this->firstValue($node, ['virtual_tour_url']),
            'source_notes' => $this->firstValue($node, ['notes']),
            'features_json' => $features,
            'has_air_conditioning' => $derivedFlags['has_air_conditioning'] ?? false,
            'has_garage' => $derivedFlags['has_garage'] ?? false,
            'has_lift' => $derivedFlags['has_lift'] ?? false,
            'has_garden' => $derivedFlags['has_garden'] ?? false,
            'has_terrace' => $derivedFlags['has_terrace'] ?? false,
            'has_sea_views' => $derivedFlags['has_sea_views'] ?? false,
            'has_parking' => $derivedFlags['has_parking'] ?? false,
            'is_furnished' => $derivedFlags['is_furnished'] ?? false,
            'has_storage_room' => $derivedFlags['has_storage_room'] ?? false,
            'has_solarium' => $derivedFlags['has_solarium'] ?? false,
            'status' => $this->normalizeStatus($this->firstValue($node, ['status'])),
            'images' => $images,
        ];
    }

    protected function resolveZona(?string $name): ?Zona
    {
        $name = trim((string) $name);

        if ($name === '') {
            return null;
        }

        $existing = Zona::query()
            ->whereRaw('LOWER(nombre) = ?', [Str::lower($name)])
            ->first();

        if ($existing) {
            return $existing;
        }

        return Zona::create([
            'nombre' => $name,
            'imagen_principal' => null,
        ]);
    }

    protected function processMappedProperty(array $mapped, int $maxImagesPerProperty = 12): array
    {
        if (! $mapped['source_listing_id']) {
            return [
                'created' => 0,
                'updated' => 0,
                'skipped' => 1,
                'images_downloaded' => 0,
            ];
        }

        return DB::transaction(function () use ($mapped, $maxImagesPerProperty) {
            $property = Property::query()
                ->where('source_name', 'kyero')
                ->where('source_listing_id', $mapped['source_listing_id'])
                ->first();

            $isNew = ! $property;
            $payloadHash = sha1(json_encode($mapped, JSON_UNESCAPED_UNICODE));

            if ($property && $property->source_payload_hash === $payloadHash) {
                $property->forceFill([
                    'source_last_synced_at' => now(),
                ])->save();

                return [
                    'created' => 0,
                    'updated' => 0,
                    'skipped' => 1,
                    'images_downloaded' => 0,
                ];
            }

            if (! $property && $mapped['ref']) {
                $property = Property::query()->where('ref', $mapped['ref'])->first();
                $isNew = ! $property;
            }

            $zona = $this->resolveZona($mapped['zona_name']);

            $attributes = [
                'title' => $mapped['title'],
                'description' => $mapped['description'],
                'description_en' => $mapped['description_en'],
                'description_fr' => $mapped['description_fr'],
                'description_de' => $mapped['description_de'],
                'description_ru' => $mapped['description_ru'],
                'description_extra' => $mapped['description_extra'] !== [] ? $mapped['description_extra'] : null,
                'location' => $mapped['location'],
                'town' => $mapped['town'],
                'province' => $mapped['province'],
                'country' => $mapped['country'],
                'location_detail' => $mapped['location_detail'],
                'latitude' => $mapped['latitude'],
                'longitude' => $mapped['longitude'],
                'price' => $mapped['price'],
                'currency' => $mapped['currency'],
                'price_freq' => $mapped['price_freq'],
                'tipo' => $mapped['tipo'],
                'bathrooms' => $mapped['bathrooms'],
                'bedrooms' => $mapped['bedrooms'],
                'area' => $mapped['area'],
                'tiene_solar' => $mapped['tiene_solar'],
                'metros_solar' => $mapped['metros_solar'],
                'tiene_patio' => $mapped['tiene_patio'],
                'tiene_piscina' => $mapped['tiene_piscina'],
                'part_ownership' => $mapped['part_ownership'],
                'leasehold' => $mapped['leasehold'],
                'new_build' => $mapped['new_build'],
                'energy_consumption' => $mapped['energy_consumption'],
                'energy_emissions' => $mapped['energy_emissions'],
                'video_url' => $mapped['video_url'],
                'virtual_tour_url' => $mapped['virtual_tour_url'],
                'source_notes' => $mapped['source_notes'],
                'features_json' => $mapped['features_json'] !== [] ? $mapped['features_json'] : null,
                'has_air_conditioning' => $mapped['has_air_conditioning'],
                'has_garage' => $mapped['has_garage'],
                'has_lift' => $mapped['has_lift'],
                'has_garden' => $mapped['has_garden'],
                'has_terrace' => $mapped['has_terrace'],
                'has_sea_views' => $mapped['has_sea_views'],
                'has_parking' => $mapped['has_parking'],
                'is_furnished' => $mapped['is_furnished'],
                'has_storage_room' => $mapped['has_storage_room'],
                'has_solarium' => $mapped['has_solarium'],
                'status' => $mapped['status'],
                'source_name' => 'kyero',
                'source_listing_id' => $mapped['source_listing_id'],
                'source_payload_hash' => $payloadHash,
                'source_last_synced_at' => now(),
                'source_date' => $mapped['source_date'],
                'zona_id' => $zona?->id,
            ];

            if ($mapped['ref']) {
                $attributes['ref'] = $mapped['ref'];
            }

            if (! $property) {
                $property = new Property();
            }

            $property->fill($attributes);
            $property->save();

            if ($mapped['ref'] && $property->ref !== $mapped['ref']) {
                $property->forceFill([
                    'ref' => $mapped['ref'],
                ])->save();
            }

            $imagesDownloaded = $this->syncImages($property, array_slice($mapped['images'], 0, $maxImagesPerProperty));

            return [
                'created' => $isNew ? 1 : 0,
                'updated' => $isNew ? 0 : 1,
                'skipped' => 0,
                'images_downloaded' => $imagesDownloaded,
            ];
        });
    }

    protected function syncImages(Property $property, array $imageUrls): int
    {
        if ($imageUrls === []) {
            return 0;
        }

        $this->deleteManagedImages($property);

        $downloaded = 0;
        $firstPath = null;

        foreach ($imageUrls as $index => $imageUrl) {
            $path = $this->downloadImage($imageUrl, $property, $index);

            if (! $path) {
                continue;
            }

            $downloaded++;

            if ($firstPath === null) {
                $firstPath = $path;
                continue;
            }

            PropertyImage::create([
                'property_id' => $property->id,
                'url' => 'storage/' . $path,
                'path' => $path,
            ]);
        }

        if ($firstPath) {
            $property->thumbnail = $firstPath;
            $property->save();
        }

        return $downloaded;
    }

    protected function deleteManagedImages(Property $property): void
    {
        if ($property->thumbnail) {
            Storage::disk('public')->delete($property->thumbnail);
        }

        foreach ($property->images as $image) {
            if ($image->path) {
                Storage::disk('public')->delete($image->path);
            }
        }

        $property->images()->delete();
    }

    protected function downloadImage(string $url, Property $property, int $index): ?string
    {
        $url = trim($url);

        if ($url === '') {
            return null;
        }

        /** @var Response $response */
        $response = Http::timeout(20)->get($url);

        if (! $response->successful()) {
            return null;
        }

        $extension = pathinfo(parse_url($url, PHP_URL_PATH) ?: '', PATHINFO_EXTENSION) ?: 'jpg';
        $filename = Str::slug($property->title ?: 'kyero-property') . '-' . $property->id . '-' . ($index + 1) . '.' . Str::lower($extension);
        $path = 'properties/imports/kyero/' . $filename;

        Storage::disk('public')->put($path, $response->body());

        return $path;
    }

    protected function firstValue(SimpleXMLElement $node, array $keys): ?string
    {
        foreach ($keys as $key) {
            $result = $node->xpath($key);

            if (is_array($result) && isset($result[0])) {
                $value = trim((string) $result[0]);

                if ($value !== '') {
                    return $value;
                }
            }

            if (isset($node->{$key})) {
                $value = trim((string) $node->{$key});

                if ($value !== '') {
                    return $value;
                }
            }
        }

        return null;
    }

    protected function flattenXPathValues(SimpleXMLElement $node, array $paths): array
    {
        $values = [];

        foreach ($paths as $path) {
            $matches = $node->xpath($path);

            if (! is_array($matches)) {
                continue;
            }

            foreach ($matches as $match) {
                $value = trim((string) $match);

                if ($value !== '') {
                    $values[] = $value;
                }
            }
        }

        return $values;
    }

    protected function flattenAttributes(SimpleXMLElement $node, array $attributes): array
    {
        $values = [];

        foreach ($node->xpath('.//*[@url]') ?: [] as $match) {
            foreach ($attributes as $attribute) {
                $value = trim((string) ($match[$attribute] ?? ''));

                if ($value !== '') {
                    $values[] = $value;
                }
            }
        }

        return $values;
    }

    protected function normalizeType(?string $type): ?string
    {
        $type = Str::lower(trim((string) $type));

        return match ($type) {
            'apartment', 'flat' => 'Piso',
            'house', 'townhouse' => 'Casa',
            'villa', 'detached villa' => 'Villa',
            'plot', 'land' => 'Solar',
            '' => null,
            default => Str::title($type),
        };
    }

    protected function buildImportedTitle(
        ?string $type,
        ?string $town,
        ?string $zonaName,
        ?string $province,
        ?string $country
    ): string {
        $typeLabel = $type ?: 'propiedad';
        $place = collect([$town, $zonaName, $province, $country])
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->first();

        return $place ? sprintf('%s en %s', $typeLabel, $place) : $typeLabel;
    }

    protected function normalizeStatus(?string $status): string
    {
        return match (Str::lower(trim((string) $status))) {
            'live', 'published', 'active', 'available' => 'published',
            'reserved' => 'reserved',
            'sold' => 'sold',
            'hidden', 'archived' => 'hidden',
            default => 'draft',
        };
    }

    protected function toInteger(?string $value): ?int
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        return (int) round((float) preg_replace('/[^0-9.,-]/', '', str_replace(',', '.', $value)));
    }

    protected function toDecimal(?string $value): ?float
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $normalized = preg_replace('/[^0-9.,-]/', '', $value);
        $normalized = str_replace('.', '', $normalized);
        $normalized = str_replace(',', '.', $normalized);

        return is_numeric($normalized) ? (float) $normalized : null;
    }

    protected function toBoolean(?string $value): bool
    {
        $value = Str::lower(trim((string) $value));

        if ($value === '') {
            return false;
        }

        if (is_numeric($value)) {
            return (float) $value > 0;
        }

        return in_array($value, ['1', 'true', 'yes', 'si'], true);
    }

    protected function toCoordinate(?string $value): ?float
    {
        $value = trim((string) $value);

        if ($value === '' || ! is_numeric(str_replace(',', '.', $value))) {
            return null;
        }

        return round((float) str_replace(',', '.', $value), 7);
    }

    protected function extractDescriptionsByLocale(SimpleXMLElement $node): array
    {
        $descriptions = [];

        foreach (['desc', 'description'] as $container) {
            if (! isset($node->{$container})) {
                continue;
            }

            foreach ($node->{$container}->children() as $localeNode) {
                $locale = Str::lower($localeNode->getName());
                $text = $this->normalizeImportedText((string) $localeNode);

                if ($locale !== '' && $text !== '') {
                    $descriptions[$locale] = $text;
                }
            }
        }

        if ($descriptions === []) {
            $fallback = $this->normalizeImportedText((string) ($node->desc ?? $node->description ?? $node->body ?? ''));

            if ($fallback !== '') {
                $descriptions['en'] = $fallback;
            }
        }

        return $descriptions;
    }

    protected function normalizeImportedText(?string $value): ?string
    {
        $value = html_entity_decode((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $value = preg_replace('/<\s*br\s*\/?>/i', "\n", $value);
        $value = preg_replace('/<\s*\/p\s*>/i', "\n\n", $value);
        $value = strip_tags((string) $value);
        $value = str_replace(["\r\n", "\r"], "\n", (string) $value);
        $value = preg_replace("/\n{3,}/", "\n\n", (string) $value);
        $value = preg_replace('/[ \t]+/', ' ', (string) $value);
        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }
}
