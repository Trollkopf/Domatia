<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'ref',
        'title',
        'location',
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
        'quick_summary_2',
        'quick_summary_3',
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
        $customItems = [
            $this->quick_summary_1,
            $this->quick_summary_2,
            $this->quick_summary_3,
        ];

        return [
            $this->sanitizeQuickSummary($customItems[0]) ?: $this->buildAutomaticSummaryType(),
            $this->sanitizeQuickSummary($customItems[1]) ?: $this->buildAutomaticSummarySpace(),
            $this->sanitizeQuickSummary($customItems[2]) ?: $this->buildAutomaticSummaryOperation(),
        ];
    }

    protected function sanitizeQuickSummary(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }

    protected function buildAutomaticSummaryType(): string
    {
        $type = $this->tipo ? mb_strtolower($this->tipo) : 'vivienda';
        $location = $this->zona->nombre ?? $this->location;

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

    protected function buildAutomaticSummarySpace(): string
    {
        $parts = [];

        if ($this->area) {
            $parts[] = number_format($this->area, 0, ',', '.') . ' m2 construidos';
        }

        if ($this->tiene_solar && $this->metros_solar) {
            $parts[] = number_format($this->metros_solar, 0, ',', '.') . ' m2 de parcela';
        }

        if ($this->tiene_patio) {
            $parts[] = 'patio exterior';
        }

        if ($this->tiene_solar && ! $this->metros_solar) {
            $parts[] = 'solar disponible';
        }

        if ($parts !== []) {
            return 'Espacios destacados: ' . collect($parts)->join(', ', ' y ') . '.';
        }

        return 'Distribución práctica y espacio preparado para adaptarse a distintos usos.';
    }

    protected function buildAutomaticSummaryOperation(): string
    {
        $statusText = match ($this->status) {
            'reserved' => 'Actualmente reservada',
            'sold' => 'Operación cerrada recientemente',
            'hidden' => 'Ficha en revisión interna',
            'draft' => 'Ficha en preparación',
            default => 'Disponible para su consulta',
        };

        $referenceText = $this->ref
            ? ' con referencia ' . $this->ref
            : '';

        return $statusText . $referenceText . ' para gestionarla de forma ágil desde el backoffice.';
    }
}
