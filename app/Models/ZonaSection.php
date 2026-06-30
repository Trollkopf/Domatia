<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class ZonaSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'zona_id',
        'titulo',
        'titulo_en',
        'titulo_fr',
        'titulo_de',
        'titulo_ru',
        'titulo_nl', 'titulo_pl', 'titulo_sv', 'titulo_da',
        'imagen',
        'descripcion',
        'descripcion_en',
        'descripcion_fr',
        'descripcion_de',
        'descripcion_ru',
        'descripcion_nl', 'descripcion_pl', 'descripcion_sv', 'descripcion_da',
    ];

    public function zona()
    {
        return $this->belongsTo(Zona::class);
    }

    public function translatedTitle(?string $locale = null): string
    {
        $locale = $locale ?: App::currentLocale();

        $localizedValue = match ($locale) {
            'en' => $this->titulo_en,
            'fr' => $this->titulo_fr,
            'de' => $this->titulo_de,
            'ru' => $this->titulo_ru,
            'nl' => $this->titulo_nl,
            'pl' => $this->titulo_pl,
            'sv' => $this->titulo_sv,
            'da' => $this->titulo_da,
            default => $this->titulo,
        };

        return filled($localizedValue) ? $localizedValue : (string) $this->titulo;
    }

    public function translatedDescription(?string $locale = null): ?string
    {
        $locale = $locale ?: App::currentLocale();

        $localizedValue = match ($locale) {
            'en' => $this->descripcion_en,
            'fr' => $this->descripcion_fr,
            'de' => $this->descripcion_de,
            'ru' => $this->descripcion_ru,
            'nl' => $this->descripcion_nl,
            'pl' => $this->descripcion_pl,
            'sv' => $this->descripcion_sv,
            'da' => $this->descripcion_da,
            default => $this->descripcion,
        };

        return filled($localizedValue) ? $localizedValue : $this->descripcion;
    }
}
