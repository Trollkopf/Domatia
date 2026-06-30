<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KyeroFeed extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'is_active',
        'max_images_per_property',
        'last_import_run_id',
        'last_status',
        'last_error',
        'last_run_at',
        'last_success_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_run_at' => 'datetime',
        'last_success_at' => 'datetime',
    ];

    public function runs()
    {
        return $this->hasMany(PropertyImportRun::class);
    }

    public function lastRun()
    {
        return $this->belongsTo(PropertyImportRun::class, 'last_import_run_id');
    }
}
