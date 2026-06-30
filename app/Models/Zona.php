<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class Zona extends Model
{
    use HasFactory;

    public const DEFAULT_IMAGE = 'images/our-company.jpg';

    protected $fillable = [
        'nombre',
        'nombre_en',
        'nombre_fr',
        'nombre_de',
        'nombre_ru',
        'nombre_nl', 'nombre_pl', 'nombre_sv', 'nombre_da',
        'imagen_principal',
        'slug',
    ];

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    public function publishedProperties()
    {
        return $this->hasMany(Property::class)->where('status', 'published');
    }

    public function representativePublishedProperty()
    {
        return $this->hasOne(Property::class)
            ->where('status', 'published')
            ->whereNotNull('thumbnail')
            ->where('thumbnail', '!=', '')
            ->latestOfMany();
    }

    public function representativeProperty()
    {
        return $this->hasOne(Property::class)
            ->whereNotNull('thumbnail')
            ->where('thumbnail', '!=', '')
            ->latestOfMany();
    }

    public function secciones()
    {
        return $this->hasMany(ZonaSection::class);
    }

    public function translatedName(?string $locale = null): string
    {
        $locale = $locale ?: App::currentLocale();

        $localizedValue = match ($locale) {
            'en' => $this->nombre_en,
            'fr' => $this->nombre_fr,
            'de' => $this->nombre_de,
            'ru' => $this->nombre_ru,
            'nl' => $this->nombre_nl,
            'pl' => $this->nombre_pl,
            'sv' => $this->nombre_sv,
            'da' => $this->nombre_da,
            default => $this->nombre,
        };

        return filled($localizedValue) ? $localizedValue : (string) $this->nombre;
    }

    public function imageUrl(): string
    {
        if ($this->imagen_principal) {
            return asset('storage/' . $this->imagen_principal);
        }

        $propertyThumbnail = $this->representativePublishedProperty?->thumbnail
            ?: $this->representativeProperty?->thumbnail;

        return $propertyThumbnail
            ? asset('storage/' . $propertyThumbnail)
            : asset(self::DEFAULT_IMAGE);
    }

    public function hasCustomImage(): bool
    {
        return filled($this->imagen_principal);
    }

    public function usesPropertyImage(): bool
    {
        return ! $this->hasCustomImage() && filled(
            $this->representativePublishedProperty?->thumbnail
                ?: $this->representativeProperty?->thumbnail
        );
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($zona) {
            $zona->slug = Str::slug($zona->nombre);
        });

        static::updating(function ($zona) {
            $zona->slug = Str::slug($zona->nombre);
        });
    }
}

