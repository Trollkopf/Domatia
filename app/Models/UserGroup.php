<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'can_access_backoffice',
        'can_manage_users',
        'can_manage_settings',
        'can_manage_properties',
        'can_publish_properties',
        'can_manage_contacts',
        'can_manage_zonas',
        'can_view_reports',
        'can_export_reports',
    ];

    protected $casts = [
        'can_access_backoffice' => 'boolean',
        'can_manage_users' => 'boolean',
        'can_manage_settings' => 'boolean',
        'can_manage_properties' => 'boolean',
        'can_publish_properties' => 'boolean',
        'can_manage_contacts' => 'boolean',
        'can_manage_zonas' => 'boolean',
        'can_view_reports' => 'boolean',
        'can_export_reports' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name) ?: 'grupo';
        $slug = $baseSlug;
        $counter = 2;

        while (static::query()
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        return $slug;
    }
}
