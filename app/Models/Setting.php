<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];

    protected static ?\Illuminate\Support\Collection $cachedSettings = null;

    public static function getValue(string $key, ?string $default = null): ?string
    {
        if (static::$cachedSettings === null) {
            static::$cachedSettings = self::query()->pluck('value', 'key');
        }

        return static::$cachedSettings[$key] ?? $default;
    }

    public static function getLocalizedValue(string $key, ?string $default = null, ?string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();

        if ($locale === config('app.locale')) {
            return static::getValue($key, $default);
        }

        $localizedValue = static::getValue($key . '_' . $locale);

        if ($localizedValue !== null && $localizedValue !== '') {
            return $localizedValue;
        }

        return static::getValue($key, $default);
    }

    public static function setValue(string $key, ?string $value): void
    {
        self::updateOrCreate(['key' => $key], ['value' => $value]);
        static::$cachedSettings = null;
    }
}
