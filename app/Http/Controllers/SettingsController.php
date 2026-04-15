<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    protected const HEADER_TARGET_WIDTH = 1600;
    protected const HEADER_TARGET_HEIGHT = 720;

    protected array $localizableKeys = [
        'footer_text',
        'contact_intro',
        'about_heading',
        'about_body',
        'home_hero_badge',
        'home_hero_title',
        'home_hero_subtitle',
        'home_search_button_text',
        'home_value_1',
        'home_value_2',
        'home_value_3',
        'home_featured_heading',
        'home_featured_subtitle',
        'home_cta_heading',
        'home_cta_body',
        'home_cta_primary_text',
        'home_cta_secondary_text',
        'about_header_title',
        'contact_header_title',
        'environment_header_title',
        'register_header_title',
    ];

    protected array $editableKeys = [
        'company_name',
        'company_phone',
        'company_email',
        'company_address',
        'footer_text',
        'contact_intro',
        'about_heading',
        'about_body',
        'home_hero_count',
        'home_hero_image_1',
        'home_hero_image_2',
        'home_hero_image_3',
        'home_hero_badge',
        'home_hero_title',
        'home_hero_subtitle',
        'home_search_button_text',
        'home_value_1',
        'home_value_2',
        'home_value_3',
        'home_featured_heading',
        'home_featured_subtitle',
        'home_cta_heading',
        'home_cta_body',
        'home_cta_primary_text',
        'home_cta_primary_url',
        'home_cta_secondary_text',
        'home_cta_secondary_url',
        'about_header_title',
        'about_header_image',
        'contact_header_title',
        'contact_header_image',
        'environment_header_title',
        'environment_header_image',
        'register_header_title',
        'register_header_image',
    ];

    protected array $imageKeys = [
        'home_hero_image_1',
        'home_hero_image_2',
        'home_hero_image_3',
        'about_header_image',
        'contact_header_image',
        'environment_header_image',
        'register_header_image',
    ];

    public function index()
    {
        $settings = $this->getEditableSettings();
        $headerImageOptions = $this->getHeaderImageOptions($settings);
        $supportedLocales = config('app.supported_locales', []);
        $defaultLocale = config('app.locale', 'es');

        return view('admin.settings.index', [
            'settings' => $settings,
            'headerImageOptions' => $headerImageOptions,
            'headerTargetWidth' => self::HEADER_TARGET_WIDTH,
            'headerTargetHeight' => self::HEADER_TARGET_HEIGHT,
            'localizedSettings' => $this->getLocalizedSettings(),
            'settingsLocales' => collect($supportedLocales)->except($defaultLocale)->all(),
        ]);
    }

    public function update(Request $request)
    {
        $settings = $this->getEditableSettings();
        $imageOptions = array_keys($this->getHeaderImageOptions($settings));

        $validated = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'company_phone' => 'nullable|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'company_address' => 'nullable|string|max:1000',
            'footer_text' => 'nullable|string|max:1000',
            'contact_intro' => 'nullable|string|max:2000',
            'about_heading' => 'nullable|string|max:255',
            'about_body' => 'nullable|string|max:5000',
            'home_hero_count' => 'nullable|integer|in:1,2,3',
            'home_hero_image_1' => 'nullable|in:' . implode(',', $imageOptions),
            'home_hero_image_2' => 'nullable|in:' . implode(',', $imageOptions),
            'home_hero_image_3' => 'nullable|in:' . implode(',', $imageOptions),
            'home_hero_badge' => 'nullable|string|max:255',
            'home_hero_title' => 'nullable|string|max:255',
            'home_hero_subtitle' => 'nullable|string|max:500',
            'home_search_button_text' => 'nullable|string|max:100',
            'home_value_1' => 'nullable|string|max:255',
            'home_value_2' => 'nullable|string|max:255',
            'home_value_3' => 'nullable|string|max:255',
            'home_featured_heading' => 'nullable|string|max:255',
            'home_featured_subtitle' => 'nullable|string|max:500',
            'home_cta_heading' => 'nullable|string|max:255',
            'home_cta_body' => 'nullable|string|max:2000',
            'home_cta_primary_text' => 'nullable|string|max:100',
            'home_cta_primary_url' => 'nullable|string|max:1000',
            'home_cta_secondary_text' => 'nullable|string|max:100',
            'home_cta_secondary_url' => 'nullable|string|max:1000',
            'about_header_title' => 'nullable|string|max:255',
            'about_header_image' => 'nullable|in:' . implode(',', $imageOptions),
            'contact_header_title' => 'nullable|string|max:255',
            'contact_header_image' => 'nullable|in:' . implode(',', $imageOptions),
            'environment_header_title' => 'nullable|string|max:255',
            'environment_header_image' => 'nullable|in:' . implode(',', $imageOptions),
            'register_header_title' => 'nullable|string|max:255',
            'register_header_image' => 'nullable|in:' . implode(',', $imageOptions),
            'home_hero_image_1_upload' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'home_hero_image_2_upload' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'home_hero_image_3_upload' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'about_header_image_upload' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'contact_header_image_upload' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'environment_header_image_upload' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'register_header_image_upload' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'home_hero_image_1_crop' => 'nullable|string|max:1000',
            'home_hero_image_2_crop' => 'nullable|string|max:1000',
            'home_hero_image_3_crop' => 'nullable|string|max:1000',
            'about_header_image_crop' => 'nullable|string|max:1000',
            'contact_header_image_crop' => 'nullable|string|max:1000',
            'environment_header_image_crop' => 'nullable|string|max:1000',
            'register_header_image_crop' => 'nullable|string|max:1000',
        ] + $this->localizedValidationRules());

        foreach ($this->editableKeys as $key) {
            if (in_array($key, $this->imageKeys, true)) {
                Setting::setValue(
                    $key,
                    $this->resolveImageSettingValue($request, $validated, $key, $settings[$key] ?? null)
                );

                continue;
            }

            Setting::setValue($key, $validated[$key] ?? null);
        }

        foreach ($this->localizedSettingKeys() as $localizedKey) {
            Setting::setValue($localizedKey, $validated[$localizedKey] ?? null);
        }

        return redirect()->route('admin.settings')->with('success', 'Ajustes actualizados correctamente.');
    }

    protected function getEditableSettings(): array
    {
        $settings = [];

        foreach ($this->editableKeys as $key) {
            $settings[$key] = Setting::getValue($key, '');
        }

        return $settings;
    }

    protected function getLocalizedSettings(): array
    {
        $settings = [];

        foreach (array_keys(config('app.supported_locales', [])) as $locale) {
            if ($locale === config('app.locale', 'es')) {
                continue;
            }

            foreach ($this->localizableKeys as $key) {
                $settings[$locale][$key] = Setting::getValue($key . '_' . $locale, '');
            }
        }

        return $settings;
    }

    protected function localizedSettingKeys(): array
    {
        $keys = [];

        foreach (array_keys(config('app.supported_locales', [])) as $locale) {
            if ($locale === config('app.locale', 'es')) {
                continue;
            }

            foreach ($this->localizableKeys as $key) {
                $keys[] = $key . '_' . $locale;
            }
        }

        return $keys;
    }

    protected function localizedValidationRules(): array
    {
        $rules = [];

        foreach (array_keys(config('app.supported_locales', [])) as $locale) {
            if ($locale === config('app.locale', 'es')) {
                continue;
            }

            foreach ($this->localizableKeys as $key) {
                $rules[$key . '_' . $locale] = match ($key) {
                    'footer_text', 'company_address' => 'nullable|string|max:1000',
                    'contact_intro', 'home_cta_body' => 'nullable|string|max:2000',
                    'about_body' => 'nullable|string|max:5000',
                    'home_hero_subtitle', 'home_featured_subtitle' => 'nullable|string|max:500',
                    'home_search_button_text', 'home_cta_primary_text', 'home_cta_secondary_text' => 'nullable|string|max:100',
                    default => 'nullable|string|max:255',
                };
            }
        }

        return $rules;
    }

    protected function getHeaderImageOptions(array $settings = []): array
    {
        $options = [
            '/images/our-company.jpg' => 'Empresa',
            '/images/images.jpg' => 'Entorno',
            '/images/domatia_logo.png' => 'Logo Domatia',
        ];

        foreach (Storage::disk('public')->files('settings/headers') as $path) {
            $options['/storage/' . $path] = 'Subida: ' . basename($path);
        }

        foreach ($this->imageKeys as $key) {
            $value = $settings[$key] ?? Setting::getValue($key);

            if ($value && ! isset($options[$value])) {
                $options[$value] = 'Actual: ' . basename(parse_url($value, PHP_URL_PATH) ?: $value);
            }
        }

        return $options;
    }

    protected function resolveImageSettingValue(Request $request, array $validated, string $key, ?string $currentValue): ?string
    {
        $uploadKey = $key . '_upload';
        $cropKey = $key . '_crop';
        $selectedValue = $validated[$key] ?? null;

        if ($request->hasFile($uploadKey)) {
            $this->deleteManagedImageIfNeeded($currentValue, null);

            return $this->storeCroppedImage($request->file($uploadKey), $request->input($cropKey), $key);
        }

        $this->deleteManagedImageIfNeeded($currentValue, $selectedValue);

        return $selectedValue;
    }

    protected function storeCroppedImage(UploadedFile $file, ?string $cropPayload, string $key): string
    {
        $crop = json_decode($cropPayload ?: '', true);

        if (! is_array($crop)) {
            $crop = [];
        }

        $sourceBinary = file_get_contents($file->getRealPath());
        $sourceImage = $sourceBinary ? imagecreatefromstring($sourceBinary) : false;

        abort_unless($sourceImage !== false, 422, 'No se ha podido procesar la imagen subida.');

        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);
        $zoom = max(1, min((float) ($crop['zoom'] ?? 1), 3));
        $offsetX = (float) ($crop['offsetX'] ?? 0);
        $offsetY = (float) ($crop['offsetY'] ?? 0);
        $baseScale = max(self::HEADER_TARGET_WIDTH / $sourceWidth, self::HEADER_TARGET_HEIGHT / $sourceHeight);
        $scaledWidth = $sourceWidth * $baseScale * $zoom;
        $scaledHeight = $sourceHeight * $baseScale * $zoom;
        $maxOffsetX = max(0, ($scaledWidth - self::HEADER_TARGET_WIDTH) / 2);
        $maxOffsetY = max(0, ($scaledHeight - self::HEADER_TARGET_HEIGHT) / 2);
        $offsetX = max(-$maxOffsetX, min($maxOffsetX, $offsetX));
        $offsetY = max(-$maxOffsetY, min($maxOffsetY, $offsetY));
        $destinationX = (self::HEADER_TARGET_WIDTH - $scaledWidth) / 2 + $offsetX;
        $destinationY = (self::HEADER_TARGET_HEIGHT - $scaledHeight) / 2 + $offsetY;

        $canvas = imagecreatetruecolor(self::HEADER_TARGET_WIDTH, self::HEADER_TARGET_HEIGHT);
        $background = imagecolorallocate($canvas, 17, 24, 39);
        imagefill($canvas, 0, 0, $background);

        imagecopyresampled(
            $canvas,
            $sourceImage,
            (int) round($destinationX),
            (int) round($destinationY),
            0,
            0,
            (int) round($scaledWidth),
            (int) round($scaledHeight),
            $sourceWidth,
            $sourceHeight
        );

        ob_start();
        imagejpeg($canvas, null, 88);
        $binary = ob_get_clean();

        imagedestroy($sourceImage);
        imagedestroy($canvas);

        $relativePath = 'settings/headers/' . Str::slug($key) . '-' . Str::uuid() . '.jpg';
        Storage::disk('public')->put($relativePath, $binary);

        return '/storage/' . $relativePath;
    }

    protected function deleteManagedImageIfNeeded(?string $currentValue, ?string $newValue): void
    {
        if (! $currentValue || $currentValue === $newValue || ! $this->isManagedHeaderImage($currentValue)) {
            return;
        }

        $diskPath = Str::after($currentValue, '/storage/');

        if ($diskPath !== '' && Storage::disk('public')->exists($diskPath)) {
            Storage::disk('public')->delete($diskPath);
        }
    }

    protected function isManagedHeaderImage(string $value): bool
    {
        return str_starts_with($value, '/storage/settings/headers/');
    }
}
