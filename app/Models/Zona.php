<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Zona extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'imagen_principal',
        'slug',
    ];

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    public function secciones()
    {
        return $this->hasMany(ZonaSection::class);
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

