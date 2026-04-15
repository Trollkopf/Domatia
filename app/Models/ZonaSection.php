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
        'imagen',
        'descripcion',
        'descripcion_en',
        'descripcion_fr',
        'descripcion_de',
        'descripcion_ru',
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
            default => $this->descripcion,
        };

        return filled($localizedValue) ? $localizedValue : $this->descripcion;
    }
}
