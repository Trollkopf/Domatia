<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contacto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'mensaje',
        'status',
        'last_contacted_at',
        'next_action_at',
        'internal_notes',
        'property_id',
    ];

    protected $casts = [
        'last_contacted_at' => 'datetime',
        'next_action_at' => 'date',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
