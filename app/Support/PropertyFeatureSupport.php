<?php

namespace App\Support;

use Illuminate\Support\Str;

class PropertyFeatureSupport
{
    public static function normalizeList(array $features): array
    {
        return collect($features)
            ->map(fn ($feature) => trim((string) $feature))
            ->filter()
            ->unique(fn (string $feature) => Str::lower($feature))
            ->values()
            ->all();
    }

    public static function inferFlags(array $features): array
    {
        $haystack = collect(self::normalizeList($features))
            ->map(fn (string $feature) => Str::lower($feature))
            ->implode(' | ');

        $matches = fn (array $needles): bool => collect($needles)
            ->contains(fn (string $needle) => str_contains($haystack, Str::lower($needle)));

        return [
            'tiene_piscina' => $matches(['pool', 'swimming pool', 'private pool', 'pool-communal', 'communal pool']),
            'tiene_patio' => $matches(['terrace', 'roof terrace', 'patio', 'garden', 'yard', 'porch', 'balcony', 'solarium']),
            'tiene_solar' => $matches(['plot', 'land', 'yard']),
            'has_air_conditioning' => $matches(['air conditioning', 'a/c', 'climate control']),
            'has_garage' => $matches(['garage']),
            'has_lift' => $matches(['lift', 'elevator']),
            'has_garden' => $matches(['garden', 'yard']),
            'has_terrace' => $matches(['terrace', 'roof terrace', 'balcony', 'porch']),
            'has_sea_views' => $matches(['sea views', 'sea view']),
            'has_parking' => $matches(['parking', 'street parking', 'off road parking']),
            'is_furnished' => $matches(['furnished', 'furniture', 'partially furnished']),
            'has_storage_room' => $matches(['storage', 'store room', 'trastero']),
            'has_solarium' => $matches(['solarium']),
        ];
    }
}
