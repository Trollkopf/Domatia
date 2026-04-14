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
}
