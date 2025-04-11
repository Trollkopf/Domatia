<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contacto extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'email', 'telefono', 'mensaje', 'property_id'];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
