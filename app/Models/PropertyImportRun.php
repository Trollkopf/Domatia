<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyImportRun extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'source_name',
        'status',
        'input_name',
        'payload_path',
        'total_properties',
        'max_images_per_property',
        'properties_seen',
        'properties_created',
        'properties_updated',
        'properties_skipped',
        'images_downloaded',
        'notes',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
