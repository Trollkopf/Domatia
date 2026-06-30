<?php

namespace Tests\Feature;

use Tests\TestCase;

class FrontendTranslationTest extends TestCase
{
    public function test_frontend_translations_have_the_same_keys_as_spanish(): void
    {
        foreach (['frontend', 'ui'] as $file) {
            $spanishKeys = array_keys($this->flatten(require lang_path("es/{$file}.php")));
            sort($spanishKeys);

            foreach (['en', 'fr', 'de', 'ru'] as $locale) {
                $localeKeys = array_keys($this->flatten(require lang_path("{$locale}/{$file}.php")));
                sort($localeKeys);

                $this->assertSame($spanishKeys, $localeKeys, "The {$locale} {$file} translations are incomplete.");
            }
        }
    }

    private function flatten(array $values, string $prefix = ''): array
    {
        $flat = [];

        foreach ($values as $key => $value) {
            $fullKey = $prefix === '' ? $key : "{$prefix}.{$key}";

            if (is_array($value)) {
                $flat += $this->flatten($value, $fullKey);
            } else {
                $flat[$fullKey] = $value;
            }
        }

        return $flat;
    }
}
