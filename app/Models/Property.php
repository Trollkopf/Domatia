<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Str;

class Property extends Model
{
    use HasFactory;

    protected $casts = [
        'is_featured' => 'boolean',
        'tiene_solar' => 'boolean',
        'tiene_patio' => 'boolean',
        'tiene_piscina' => 'boolean',
        'part_ownership' => 'boolean',
        'leasehold' => 'boolean',
        'new_build' => 'boolean',
        'features_json' => 'array',
        'description_extra' => 'array',
        'has_air_conditioning' => 'boolean',
        'has_garage' => 'boolean',
        'has_lift' => 'boolean',
        'has_garden' => 'boolean',
        'has_terrace' => 'boolean',
        'has_sea_views' => 'boolean',
        'has_parking' => 'boolean',
        'is_furnished' => 'boolean',
        'has_storage_room' => 'boolean',
        'has_solarium' => 'boolean',
        'source_date' => 'datetime',
        'source_last_synced_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    protected $fillable = [
        'ref',
        'title',
        'title_en',
        'title_fr',
        'title_de',
        'title_ru',
        'title_nl',
        'title_pl',
        'title_sv',
        'title_da',
        'location',
        'location_en',
        'location_fr',
        'location_de',
        'location_ru',
        'location_nl',
        'location_pl',
        'location_sv',
        'location_da',
        'price',
        'tipo',
        'is_featured',
        'status',
        'description',
        'description_en',
        'description_fr',
        'description_de',
        'description_ru',
        'description_nl',
        'description_pl',
        'description_sv',
        'description_da',
        'thumbnail',
        'zona_id',
        'propietario_id',
        'bathrooms',
        'bedrooms',
        'area',
        'tiene_solar',
        'metros_solar',
        'tiene_patio',
        'tiene_piscina',
        'quick_summary_1',
        'quick_summary_1_en',
        'quick_summary_1_fr',
        'quick_summary_1_de',
        'quick_summary_1_ru',
        'quick_summary_1_nl', 'quick_summary_1_pl', 'quick_summary_1_sv', 'quick_summary_1_da',
        'quick_summary_2',
        'quick_summary_2_en',
        'quick_summary_2_fr',
        'quick_summary_2_de',
        'quick_summary_2_ru',
        'quick_summary_2_nl', 'quick_summary_2_pl', 'quick_summary_2_sv', 'quick_summary_2_da',
        'quick_summary_3',
        'quick_summary_3_en',
        'quick_summary_3_fr',
        'quick_summary_3_de',
        'quick_summary_3_ru',
        'quick_summary_3_nl', 'quick_summary_3_pl', 'quick_summary_3_sv', 'quick_summary_3_da',
        'source_name',
        'source_listing_id',
        'source_payload_hash',
        'source_last_synced_at',
        'source_date',
        'currency',
        'price_freq',
        'part_ownership',
        'leasehold',
        'new_build',
        'town',
        'province',
        'country',
        'location_detail',
        'latitude',
        'longitude',
        'energy_consumption',
        'energy_emissions',
        'video_url',
        'virtual_tour_url',
        'source_notes',
        'features_json',
        'description_extra',
        'has_air_conditioning',
        'has_garage',
        'has_lift',
        'has_garden',
        'has_terrace',
        'has_sea_views',
        'has_parking',
        'is_furnished',
        'has_storage_room',
        'has_solarium',
    ];

    public function zona()
    {
        return $this->belongsTo(Zona::class);
    }

    public function propietario()
    {
        return $this->belongsTo(Propietario::class);
    }

    public function contactos()
    {
        return $this->hasMany(Contacto::class);
    }

    protected static function booted()
    {
        static::created(function ($property) {
            $property->update(['ref' => "R-{$property->id}"]);
        });

        static::creating(function ($property) {
            $baseSlug = Str::slug($property->title);
            $slug = $baseSlug;
            $count = 2;

            // Verifica unicidad
            while (Property::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $count++;
            }

            $property->slug = $slug;

            // Ref automática si no se ha generado aún
            if (empty($property->ref)) {
                $property->ref = "R-{$property->id}";
            }
        });
    }

    public function images()
    {
        return $this->hasMany(PropertyImage::class);
    }

    public function quickSummaryItems(): array
    {
        $locale = App::currentLocale();
        $customItems = [
            $this->translatedQuickSummary(1, $locale),
            $this->translatedQuickSummary(2, $locale),
            $this->translatedQuickSummary(3, $locale),
        ];

        return [
            $this->sanitizeQuickSummary($customItems[0]) ?: $this->buildAutomaticSummaryType($locale),
            $this->sanitizeQuickSummary($customItems[1]) ?: $this->buildAutomaticSummarySpace($locale),
            $this->sanitizeQuickSummary($customItems[2]) ?: $this->buildAutomaticSummaryOperation($locale),
        ];
    }

    public function translatedDescription(?string $locale = null): string
    {
        $locale = $locale ?: App::currentLocale();
        $extraDescriptions = (array) ($this->description_extra ?? []);

        $localizedValue = match ($locale) {
            'en' => $this->description_en,
            'fr' => $this->description_fr,
            'de' => $this->description_de,
            'ru' => $this->description_ru,
            'nl' => $this->description_nl,
            'pl' => $this->description_pl,
            'sv' => $this->description_sv,
            'da' => $this->description_da,
            default => $this->description,
        };

        return $this->sanitizeQuickSummary($localizedValue)
            ?: $this->sanitizeQuickSummary($extraDescriptions[$locale] ?? null)
            ?: (string) $this->description;
    }

    public function translatedTitle(?string $locale = null): string
    {
        $locale = $locale ?: App::currentLocale();

        $localizedValue = match ($locale) {
            'en' => $this->title_en,
            'fr' => $this->title_fr,
            'de' => $this->title_de,
            'ru' => $this->title_ru,
            'nl' => $this->title_nl,
            'pl' => $this->title_pl,
            'sv' => $this->title_sv,
            'da' => $this->title_da,
            default => $this->title,
        };

        return $this->sanitizeQuickSummary($localizedValue) ?: (string) $this->title;
    }

    public function translatedLocation(?string $locale = null): ?string
    {
        $locale = $locale ?: App::currentLocale();

        $localizedValue = match ($locale) {
            'en' => $this->location_en,
            'fr' => $this->location_fr,
            'de' => $this->location_de,
            'ru' => $this->location_ru,
            'nl' => $this->location_nl,
            'pl' => $this->location_pl,
            'sv' => $this->location_sv,
            'da' => $this->location_da,
            default => $this->location,
        };

        return $this->sanitizeQuickSummary($localizedValue) ?: $this->location;
    }

    public function featuresList(): array
    {
        return collect((array) ($this->features_json ?? []))
            ->map(fn ($feature) => trim((string) $feature))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function translatedFeaturesList(?string $locale = null): array
    {
        $translationKeys = [
            'pool' => 'pool',
            'swimming-pool' => 'pool',
            'private-pool' => 'pool',
            'air-conditioning' => 'air_conditioning',
            'air-conditioner' => 'air_conditioning',
            'garage' => 'garage',
            'lift' => 'lift',
            'elevator' => 'lift',
            'parking' => 'parking',
            'terrace' => 'terrace',
            'garden' => 'garden',
            'solarium' => 'solarium',
            'storage-room' => 'storage_room',
            'furnished' => 'furnished',
            'sea-views' => 'sea_views',
            'sea-view' => 'sea_views',
            'new-build' => 'new_build',
        ];

        return collect($this->featuresList())
            ->map(function (string $feature) use ($locale, $translationKeys) {
                $key = $translationKeys[Str::slug($feature)] ?? null;

                return $key
                    ? __('frontend.properties.features.' . $key, locale: $locale ?: App::currentLocale())
                    : $feature;
            })
            ->unique()
            ->values()
            ->all();
    }

    public function hasCoordinates(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    public function fullLocationLabel(): string
    {
        return collect([
            $this->translatedLocation(),
            $this->town,
            $this->province,
            $this->country,
        ])->filter()->unique()->implode(', ');
    }

    public function translatedTypeLabel(?string $locale = null): string
    {
        $type = (string) $this->tipo;
        $translationKey = Str::lower($type);

        if ($type === '') {
            return __('ui.properties.type_fallback', locale: $locale ?: App::currentLocale());
        }

        $translated = __('ui.property_types.' . $translationKey, locale: $locale ?: App::currentLocale());

        return $translated === 'ui.property_types.' . $translationKey
            ? ucfirst($type)
            : $translated;
    }

    public function setTipoAttribute($value): void
    {
        $value = trim((string) $value);

        $this->attributes['tipo'] = $value !== '' ? Str::title(Str::lower($value)) : null;
    }

    protected function sanitizeQuickSummary(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }

    protected function translatedQuickSummary(int $index, string $locale): ?string
    {
        $field = 'quick_summary_' . $index;

        $localizedField = match ($locale) {
            'en' => $field . '_en',
            'fr' => $field . '_fr',
            'de' => $field . '_de',
            'ru' => $field . '_ru',
            'nl' => $field . '_nl',
            'pl' => $field . '_pl',
            'sv' => $field . '_sv',
            'da' => $field . '_da',
            default => $field,
        };

        return $this->sanitizeQuickSummary($this->{$localizedField} ?? null)
            ?: $this->sanitizeQuickSummary($this->{$field} ?? null);
    }

    protected function buildAutomaticSummaryType(string $locale): string
    {
        $type = $this->tipo ? mb_strtolower($this->tipo) : 'vivienda';
        $location = $this->zona?->translatedName($locale) ?? $this->translatedLocation($locale);
        $translatedType = mb_strtolower($this->translatedTypeLabel($locale));

        return match ($locale) {
            'en' => $this->buildEnglishTypeSummary($translatedType, $location),
            'fr' => $this->buildFrenchTypeSummary($translatedType, $location),
            'de' => $this->buildGermanTypeSummary($translatedType, $location),
            'ru' => $this->buildRussianTypeSummary($translatedType, $location),
            'nl', 'pl', 'sv', 'da' => $this->buildAdditionalLocaleTypeSummary($locale, $translatedType, $location),
            default => $this->buildSpanishTypeSummary($type, $location),
        };
    }

    protected function buildAutomaticSummarySpace(string $locale): string
    {
        $parts = [];

        if ($this->area) {
            $parts[] = number_format($this->area, 0, ',', '.') . ' m²';
        }

        if ($this->tiene_solar && $this->metros_solar) {
            $parts[] = number_format($this->metros_solar, 0, ',', '.') . ' m²';
        }

        return match ($locale) {
            'en' => $this->buildEnglishSpaceSummary($parts),
            'fr' => $this->buildFrenchSpaceSummary($parts),
            'de' => $this->buildGermanSpaceSummary($parts),
            'ru' => $this->buildRussianSpaceSummary($parts),
            'nl', 'pl', 'sv', 'da' => $this->buildAdditionalLocaleSpaceSummary($locale),
            default => $this->buildSpanishSpaceSummary($parts),
        };
    }

    protected function buildAutomaticSummaryOperation(string $locale): string
    {
        return match ($locale) {
            'en' => match ($this->status) {
                'reserved' => 'Currently reserved. Ask us about availability or similar homes nearby.',
                'sold' => 'This home has been sold. We can help you find similar opportunities.',
                default => 'Available. Request more information or arrange a viewing with no obligation.',
            },
            'fr' => match ($this->status) {
                'reserved' => 'Actuellement réservé. Consultez-nous sur sa disponibilité ou sur des biens similaires.',
                'sold' => 'Ce bien a été vendu. Nous pouvons vous proposer des alternatives similaires.',
                default => 'Disponible. Demandez plus d’informations ou organisez une visite sans engagement.',
            },
            'de' => match ($this->status) {
                'reserved' => 'Derzeit reserviert. Fragen Sie uns nach der Verfügbarkeit oder ähnlichen Immobilien.',
                'sold' => 'Diese Immobilie wurde verkauft. Wir helfen Ihnen gerne bei ähnlichen Angeboten.',
                default => 'Verfügbar. Fordern Sie weitere Informationen an oder vereinbaren Sie unverbindlich einen Besichtigungstermin.',
            },
            'ru' => match ($this->status) {
                'reserved' => 'Сейчас зарезервировано. Уточните доступность или попросите подобрать похожие варианты.',
                'sold' => 'Этот объект продан. Мы поможем подобрать похожие предложения.',
                default => 'Доступно. Запросите подробности или договоритесь о просмотре без обязательств.',
            },
            'nl' => match ($this->status) {
                'reserved' => 'Momenteel gereserveerd. Vraag ons naar de beschikbaarheid of vergelijkbare woningen.',
                'sold' => 'Deze woning is verkocht. Wij helpen u graag vergelijkbare opties te vinden.',
                default => 'Beschikbaar. Vraag meer informatie aan of plan vrijblijvend een bezichtiging.',
            },
            'pl' => match ($this->status) {
                'reserved' => 'Obecnie zarezerwowana. Zapytaj o dostępność lub podobne nieruchomości.',
                'sold' => 'Ta nieruchomość została sprzedana. Pomożemy znaleźć podobne oferty.',
                default => 'Dostępna. Poproś o więcej informacji lub umów niezobowiązującą wizytę.',
            },
            'sv' => match ($this->status) {
                'reserved' => 'För närvarande reserverad. Fråga oss om tillgänglighet eller liknande bostäder.',
                'sold' => 'Bostaden är såld. Vi hjälper dig gärna att hitta liknande alternativ.',
                default => 'Tillgänglig. Begär mer information eller boka en förutsättningslös visning.',
            },
            'da' => match ($this->status) {
                'reserved' => 'Reserveret i øjeblikket. Spørg os om tilgængelighed eller lignende boliger.',
                'sold' => 'Boligen er solgt. Vi hjælper dig gerne med at finde lignende muligheder.',
                default => 'Tilgængelig. Bed om flere oplysninger eller aftal en uforpligtende fremvisning.',
            },
            default => match ($this->status) {
                'reserved' => 'Actualmente reservada. Consúltanos su disponibilidad o descubre alternativas similares.',
                'sold' => 'Esta vivienda ya se ha vendido. Podemos ayudarte a encontrar opciones similares.',
                default => 'Disponible. Solicita más información o concierta una visita sin compromiso.',
            },
        };
    }

    protected function buildSpanishTypeSummary(string $type, ?string $location): string
    {
        $extras = collect([
            $this->bedrooms ? $this->bedrooms . ' dormitorio' . ($this->bedrooms === 1 ? '' : 's') : null,
            $this->bathrooms ? $this->bathrooms . ' baño' . ($this->bathrooms === 1 ? '' : 's') : null,
            $this->tiene_piscina ? 'piscina' : null,
        ])->filter()->values();

        if ($extras->isNotEmpty()) {
            return 'Una ' . $type . ' con ' . $extras->join(', ', ' y ') . ($location ? ' en ' . $location : '') . '.';
        }

        return 'Una ' . $type . ($location ? ' ubicada en ' . $location : '') . ' lista para valorar con calma.';
    }

    protected function buildEnglishTypeSummary(string $type, ?string $location): string
    {
        $extras = collect([
            $this->bedrooms ? $this->bedrooms . ' bedroom' . ($this->bedrooms === 1 ? '' : 's') : null,
            $this->bathrooms ? $this->bathrooms . ' bathroom' . ($this->bathrooms === 1 ? '' : 's') : null,
            $this->tiene_piscina ? 'pool' : null,
        ])->filter()->values();

        if ($extras->isNotEmpty()) {
            return 'A ' . $type . ' with ' . $extras->join(', ', ' and ') . ($location ? ' in ' . $location : '') . '.';
        }

        return 'A ' . $type . ($location ? ' located in ' . $location : '') . ' ready to be explored in detail.';
    }

    protected function buildFrenchTypeSummary(string $type, ?string $location): string
    {
        $extras = collect([
            $this->bedrooms ? $this->bedrooms . ' chambre' . ($this->bedrooms === 1 ? '' : 's') : null,
            $this->bathrooms ? $this->bathrooms . ' salle' . ($this->bathrooms === 1 ? ' de bain' : 's de bain') : null,
            $this->tiene_piscina ? 'piscine' : null,
        ])->filter()->values();

        if ($extras->isNotEmpty()) {
            return 'Un bien de type ' . $type . ' avec ' . $extras->join(', ', ' et ') . ($location ? ' à ' . $location : '') . '.';
        }

        return 'Un bien de type ' . $type . ($location ? ' situé à ' . $location : '') . ' prêt à être étudié sereinement.';
    }

    protected function buildGermanTypeSummary(string $type, ?string $location): string
    {
        $extras = collect([
            $this->bedrooms ? $this->bedrooms . ' Schlafzimmer' : null,
            $this->bathrooms ? $this->bathrooms . ' Bad' . ($this->bathrooms === 1 ? '' : 'er') : null,
            $this->tiene_piscina ? 'Pool' : null,
        ])->filter()->values();

        if ($extras->isNotEmpty()) {
            return 'Eine ' . $type . ' mit ' . $extras->join(', ', ' und ') . ($location ? ' in ' . $location : '') . '.';
        }

        return 'Eine ' . $type . ($location ? ' in ' . $location : '') . ' für eine ruhige Bewertung vorbereitet.';
    }

    protected function buildRussianTypeSummary(string $type, ?string $location): string
    {
        $extras = collect([
            $this->bedrooms ? $this->bedrooms . ' спальн.' : null,
            $this->bathrooms ? $this->bathrooms . ' ванн.' : null,
            $this->tiene_piscina ? 'бассейн' : null,
        ])->filter()->values();

        if ($extras->isNotEmpty()) {
            return 'Объект типа ' . $type . ' с ' . $extras->join(', ', ' и ') . ($location ? ' в ' . $location : '') . '.';
        }

        return 'Объект типа ' . $type . ($location ? ' в ' . $location : '') . ' готов к детальному просмотру.';
    }

    protected function buildSpanishSpaceSummary(array $parts): string
    {
        $details = [];

        if ($this->area) {
            $details[] = number_format($this->area, 0, ',', '.') . ' m² construidos';
        }
        if ($this->tiene_solar && $this->metros_solar) {
            $details[] = number_format($this->metros_solar, 0, ',', '.') . ' m² de parcela';
        }
        if ($this->tiene_patio) {
            $details[] = 'patio exterior';
        }
        if ($this->tiene_solar && ! $this->metros_solar) {
            $details[] = 'solar disponible';
        }

        if ($details !== []) {
            return 'Espacios destacados: ' . collect($details)->join(', ', ' y ') . '.';
        }

        return 'Distribución práctica y espacio preparado para adaptarse a distintos usos.';
    }

    protected function buildEnglishSpaceSummary(array $parts): string
    {
        $details = [];

        if ($this->area) {
            $details[] = number_format($this->area, 0, ',', '.') . ' sqm built';
        }
        if ($this->tiene_solar && $this->metros_solar) {
            $details[] = number_format($this->metros_solar, 0, ',', '.') . ' sqm plot';
        }
        if ($this->tiene_patio) {
            $details[] = 'outdoor patio';
        }
        if ($this->tiene_solar && ! $this->metros_solar) {
            $details[] = 'plot available';
        }

        if ($details !== []) {
            return 'Key spaces: ' . collect($details)->join(', ', ' and ') . '.';
        }

        return 'A practical layout with space ready to adapt to different lifestyles.';
    }

    protected function buildFrenchSpaceSummary(array $parts): string
    {
        $details = [];

        if ($this->area) {
            $details[] = number_format($this->area, 0, ',', '.') . ' m² habitables';
        }
        if ($this->tiene_solar && $this->metros_solar) {
            $details[] = number_format($this->metros_solar, 0, ',', '.') . ' m² de terrain';
        }
        if ($this->tiene_patio) {
            $details[] = 'patio extérieur';
        }
        if ($this->tiene_solar && ! $this->metros_solar) {
            $details[] = 'terrain disponible';
        }

        if ($details !== []) {
            return 'Espaces à retenir : ' . collect($details)->join(', ', ' et ') . '.';
        }

        return 'Une distribution fonctionnelle qui s’adapte à différents usages.';
    }

    protected function buildGermanSpaceSummary(array $parts): string
    {
        $details = [];

        if ($this->area) {
            $details[] = number_format($this->area, 0, ',', '.') . ' m² Wohnfläche';
        }
        if ($this->tiene_solar && $this->metros_solar) {
            $details[] = number_format($this->metros_solar, 0, ',', '.') . ' m² Grundstück';
        }
        if ($this->tiene_patio) {
            $details[] = 'Außenbereich';
        }
        if ($this->tiene_solar && ! $this->metros_solar) {
            $details[] = 'Grundstück vorhanden';
        }

        if ($details !== []) {
            return 'Wichtige Flächen: ' . collect($details)->join(', ', ' und ') . '.';
        }

        return 'Praktischer Grundriss mit flexibler Nutzbarkeit für verschiedene Lebensstile.';
    }

    protected function buildRussianSpaceSummary(array $parts): string
    {
        $details = [];

        if ($this->area) {
            $details[] = number_format($this->area, 0, ',', '.') . ' м2 жилой площади';
        }
        if ($this->tiene_solar && $this->metros_solar) {
            $details[] = number_format($this->metros_solar, 0, ',', '.') . ' м2 участка';
        }
        if ($this->tiene_patio) {
            $details[] = 'внутренний двор';
        }
        if ($this->tiene_solar && ! $this->metros_solar) {
            $details[] = 'есть участок';
        }

        if ($details !== []) {
            return 'Ключевые пространства: ' . collect($details)->join(', ', ' и ') . '.';
        }

        return 'Практичная планировка и пространство, которое легко адаптировать под разные сценарии жизни.';
    }

    protected function buildAdditionalLocaleTypeSummary(string $locale, string $type, ?string $location): string
    {
        $copy = match ($locale) {
            'nl' => ['bedroom' => 'slaapkamer', 'bedrooms' => 'slaapkamers', 'bathroom' => 'badkamer', 'bathrooms' => 'badkamers', 'pool' => 'zwembad', 'prefix' => 'Een', 'with' => 'met', 'in' => 'in', 'fallback' => 'klaar om rustig te ontdekken'],
            'pl' => ['bedroom' => 'sypialnia', 'bedrooms' => 'sypialnie', 'bathroom' => 'łazienka', 'bathrooms' => 'łazienki', 'pool' => 'basen', 'prefix' => 'Nieruchomość typu', 'with' => 'z', 'in' => 'w', 'fallback' => 'gotowa do dokładnego obejrzenia'],
            'sv' => ['bedroom' => 'sovrum', 'bedrooms' => 'sovrum', 'bathroom' => 'badrum', 'bathrooms' => 'badrum', 'pool' => 'pool', 'prefix' => 'En', 'with' => 'med', 'in' => 'i', 'fallback' => 'redo att upptäckas i lugn och ro'],
            default => ['bedroom' => 'soveværelse', 'bedrooms' => 'soveværelser', 'bathroom' => 'badeværelse', 'bathrooms' => 'badeværelser', 'pool' => 'pool', 'prefix' => 'En', 'with' => 'med', 'in' => 'i', 'fallback' => 'klar til at blive udforsket nærmere'],
        };
        $extras = collect([
            $this->bedrooms ? $this->bedrooms . ' ' . ($this->bedrooms === 1 ? $copy['bedroom'] : $copy['bedrooms']) : null,
            $this->bathrooms ? $this->bathrooms . ' ' . ($this->bathrooms === 1 ? $copy['bathroom'] : $copy['bathrooms']) : null,
            $this->tiene_piscina ? $copy['pool'] : null,
        ])->filter()->values();
        $locationText = $location ? ' ' . $copy['in'] . ' ' . $location : '';
        $conjunction = match ($locale) { 'pl' => 'i', 'nl' => 'en', 'sv' => 'och', default => 'og' };

        return $copy['prefix'] . ' ' . $type
            . ($extras->isNotEmpty() ? ' ' . $copy['with'] . ' ' . $extras->join(', ', ' ' . $conjunction . ' ') : '')
            . $locationText . ($extras->isEmpty() ? ' ' . $copy['fallback'] : '') . '.';
    }

    protected function buildAdditionalLocaleSpaceSummary(string $locale): string
    {
        $copy = match ($locale) {
            'nl' => ['built' => 'm² woonoppervlakte', 'plot' => 'm² perceel', 'patio' => 'buitenpatio', 'available' => 'perceel beschikbaar', 'prefix' => 'Belangrijkste ruimtes:', 'fallback' => 'Een praktische indeling die zich aan verschillende woonwensen aanpast.', 'and' => 'en'],
            'pl' => ['built' => 'm² powierzchni', 'plot' => 'm² działki', 'patio' => 'zewnętrzne patio', 'available' => 'dostępna działka', 'prefix' => 'Najważniejsze przestrzenie:', 'fallback' => 'Praktyczny układ, który można dopasować do różnych potrzeb.', 'and' => 'i'],
            'sv' => ['built' => 'm² boyta', 'plot' => 'm² tomt', 'patio' => 'uteplats', 'available' => 'tomt tillgänglig', 'prefix' => 'Viktiga ytor:', 'fallback' => 'En praktisk planlösning som kan anpassas till olika behov.', 'and' => 'och'],
            default => ['built' => 'm² boligareal', 'plot' => 'm² grund', 'patio' => 'gårdhave', 'available' => 'grund tilgængelig', 'prefix' => 'Vigtige arealer:', 'fallback' => 'En praktisk planløsning, der kan tilpasses forskellige behov.', 'and' => 'og'],
        };
        $details = collect([
            $this->area ? number_format($this->area, 0, ',', '.') . ' ' . $copy['built'] : null,
            $this->tiene_solar && $this->metros_solar ? number_format($this->metros_solar, 0, ',', '.') . ' ' . $copy['plot'] : null,
            $this->tiene_patio ? $copy['patio'] : null,
            $this->tiene_solar && ! $this->metros_solar ? $copy['available'] : null,
        ])->filter()->values();

        return $details->isNotEmpty() ? $copy['prefix'] . ' ' . $details->join(', ', ' ' . $copy['and'] . ' ') . '.' : $copy['fallback'];
    }
}
