<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontendTranslationTest extends TestCase
{
    use RefreshDatabase;

    public function test_frontend_translations_have_the_same_keys_as_spanish(): void
    {
        foreach (['frontend', 'ui'] as $file) {
            $spanishKeys = array_keys($this->flatten(require lang_path("es/{$file}.php")));
            sort($spanishKeys);

            foreach (array_keys(config('app.supported_locales')) as $locale) {
                if ($locale === 'es') {
                    continue;
                }
                $localeKeys = array_keys($this->flatten(require lang_path("{$locale}/{$file}.php")));
                sort($localeKeys);

                $this->assertSame($spanishKeys, $localeKeys, "The {$locale} {$file} translations are incomplete.");
            }
        }
    }

    public function test_home_uses_localized_defaults_when_no_custom_translation_exists(): void
    {
        $this->withSession(['locale' => 'ru'])
            ->get('/')
            ->assertOk()
            ->assertSee('Откройте для себя исключительную недвижимость');
    }

    public function test_framework_messages_are_available_in_every_supported_locale(): void
    {
        foreach (array_keys(config('app.supported_locales')) as $locale) {
            $this->app->setLocale($locale);

            $this->assertStringNotContainsString('validation.', __('validation.required'));
            $this->assertStringNotContainsString('auth.', __('auth.failed'));
            $this->assertStringNotContainsString('passwords.', __('passwords.sent'));
            $this->assertStringNotContainsString('pagination.', __('pagination.next'));
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
