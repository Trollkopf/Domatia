<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'user_group_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function userGroup()
    {
        return $this->belongsTo(UserGroup::class);
    }

    public function resolvedGroup(): ?UserGroup
    {
        if ($this->relationLoaded('userGroup')) {
            return $this->userGroup ?: UserGroup::query()->where('slug', $this->role ?: 'user')->first();
        }

        if ($this->user_group_id) {
            return $this->userGroup ?: UserGroup::query()->where('slug', $this->role ?: 'user')->first();
        }

        return UserGroup::query()->where('slug', $this->role ?: 'user')->first();
    }

    public function groupLabel(): string
    {
        return $this->resolvedGroup()?->name ?: match ($this->role) {
            'admin' => 'Administradores',
            'moderator' => 'Moderadores',
            'commercial' => 'Comerciales',
            default => 'Usuarios',
        };
    }

    public function canAccessBackoffice(): bool
    {
        return (bool) ($this->resolvedGroup()?->can_access_backoffice ?? ($this->role === 'admin'));
    }

    public function canManageUsers(): bool
    {
        return (bool) ($this->resolvedGroup()?->can_manage_users ?? ($this->role === 'admin'));
    }

    public function canManageSettings(): bool
    {
        return (bool) ($this->resolvedGroup()?->can_manage_settings ?? ($this->role === 'admin'));
    }

    public function canManageProperties(): bool
    {
        return (bool) ($this->resolvedGroup()?->can_manage_properties ?? ($this->role === 'admin'));
    }

    public function canPublishProperties(): bool
    {
        return (bool) ($this->resolvedGroup()?->can_publish_properties ?? ($this->role === 'admin'));
    }

    public function canManageContacts(): bool
    {
        return (bool) ($this->resolvedGroup()?->can_manage_contacts ?? ($this->role === 'admin'));
    }

    public function canManageZonas(): bool
    {
        return (bool) ($this->resolvedGroup()?->can_manage_zonas ?? ($this->role === 'admin'));
    }

    public function canViewReports(): bool
    {
        return (bool) ($this->resolvedGroup()?->can_view_reports ?? ($this->role === 'admin'));
    }

    public function canExportReports(): bool
    {
        return (bool) ($this->resolvedGroup()?->can_export_reports ?? ($this->role === 'admin'));
    }
}
