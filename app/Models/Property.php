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
        'location',
        'location_en',
        'location_fr',
        'location_de',
        'location_ru',
        'price',
        'tipo',
        'is_featured',
        'status',
        'description',
        'description_en',
        'description_fr',
        'description_de',
        'description_ru',
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
        'quick_summary_2',
        'quick_summary_2_en',
        'quick_summary_2_fr',
        'quick_summary_2_de',
        'quick_summary_2_ru',
        'quick_summary_3',
        'quick_summary_3_en',
        'quick_summary_3_fr',
        'quick_summary_3_de',
        'quick_summary_3_ru',
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
            default => $this->buildSpanishTypeSummary($type, $location),
        };
    }

    protected function buildAutomaticSummarySpace(string $locale): string
    {
        $parts = [];

        if ($this->area) {
            $parts[] = number_format($this->area, 0, ',', '.') . ' m2';
        }

        if ($this->tiene_solar && $this->metros_solar) {
            $parts[] = number_format($this->metros_solar, 0, ',', '.') . ' m2';
        }

        return match ($locale) {
            'en' => $this->buildEnglishSpaceSummary($parts),
            'fr' => $this->buildFrenchSpaceSummary($parts),
            'de' => $this->buildGermanSpaceSummary($parts),
            'ru' => $this->buildRussianSpaceSummary($parts),
            default => $this->buildSpanishSpaceSummary($parts),
        };
    }

    protected function buildAutomaticSummaryOperation(string $locale): string
    {
        $statusText = match ($locale) {
            'en' => match ($this->status) {
                'reserved' => 'Currently reserved',
                'sold' => 'Recently sold',
                'hidden' => 'Internal review listing',
                'draft' => 'Listing in preparation',
                default => 'Available for enquiries',
            },
            'fr' => match ($this->status) {
                'reserved' => 'Actuellement reservee',
                'sold' => 'Operation finalisee recemment',
                'hidden' => 'Fiche en revision interne',
                'draft' => 'Fiche en preparation',
                default => 'Disponible pour information',
            },
            'de' => match ($this->status) {
                'reserved' => 'Derzeit reserviert',
                'sold' => 'Vor Kurzem verkauft',
                'hidden' => 'Interne Prufung',
                'draft' => 'Expose in Vorbereitung',
                default => 'Fur Anfragen verfugbar',
            },
            'ru' => match ($this->status) {
                'reserved' => 'Сейчас в резерве',
                'sold' => 'Недавно продано',
                'hidden' => 'Объект на внутренней проверке',
                'draft' => 'Карточка готовится',
                default => 'Доступно для запроса',
            },
            default => match ($this->status) {
                'reserved' => 'Actualmente reservada',
                'sold' => 'Operación cerrada recientemente',
                'hidden' => 'Ficha en revisión interna',
                'draft' => 'Ficha en preparación',
                default => 'Disponible para su consulta',
            },
        };

        return match ($locale) {
            'en' => $statusText . ($this->ref ? ' with reference ' . $this->ref : '') . ' for quick follow-up.',
            'fr' => $statusText . ($this->ref ? ' avec la reference ' . $this->ref : '') . ' pour un suivi rapide.',
            'de' => $statusText . ($this->ref ? ' mit Referenz ' . $this->ref : '') . ' fur eine schnelle Bearbeitung.',
            'ru' => $statusText . ($this->ref ? ' с номером ' . $this->ref : '') . ' для быстрого сопровождения.',
            default => $statusText . ($this->ref ? ' con referencia ' . $this->ref : '') . ' para gestionarla de forma ágil desde el backoffice.',
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
            return 'Un bien de type ' . $type . ' avec ' . $extras->join(', ', ' et ') . ($location ? ' a ' . $location : '') . '.';
        }

        return 'Un bien de type ' . $type . ($location ? ' situe a ' . $location : '') . ' pret a etre etudie sereinement.';
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

        return 'Eine ' . $type . ($location ? ' in ' . $location : '') . ' fur eine ruhige Bewertung vorbereitet.';
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
            $details[] = number_format($this->area, 0, ',', '.') . ' m2 construidos';
        }
        if ($this->tiene_solar && $this->metros_solar) {
            $details[] = number_format($this->metros_solar, 0, ',', '.') . ' m2 de parcela';
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
            $details[] = number_format($this->area, 0, ',', '.') . ' m2 habitables';
        }
        if ($this->tiene_solar && $this->metros_solar) {
            $details[] = number_format($this->metros_solar, 0, ',', '.') . ' m2 de terrain';
        }
        if ($this->tiene_patio) {
            $details[] = 'patio exterieur';
        }
        if ($this->tiene_solar && ! $this->metros_solar) {
            $details[] = 'terrain disponible';
        }

        if ($details !== []) {
            return 'Espaces a retenir: ' . collect($details)->join(', ', ' et ') . '.';
        }

        return 'Une distribution fonctionnelle qui s adapte a differents usages.';
    }

    protected function buildGermanSpaceSummary(array $parts): string
    {
        $details = [];

        if ($this->area) {
            $details[] = number_format($this->area, 0, ',', '.') . ' m2 Wohnflache';
        }
        if ($this->tiene_solar && $this->metros_solar) {
            $details[] = number_format($this->metros_solar, 0, ',', '.') . ' m2 Grundstuck';
        }
        if ($this->tiene_patio) {
            $details[] = 'AuBenpatio';
        }
        if ($this->tiene_solar && ! $this->metros_solar) {
            $details[] = 'Grundstuck vorhanden';
        }

        if ($details !== []) {
            return 'Wichtige Flachen: ' . collect($details)->join(', ', ' und ') . '.';
        }

        return 'Praktischer Grundriss mit flexibler Nutzbarkeit fur verschiedene Lebensstile.';
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
}
