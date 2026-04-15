<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SettingsImageUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_and_store_a_cropped_header_image(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this
            ->actingAs($admin)
            ->put(route('admin.settings.update'), [
                'company_name' => 'Domatia',
                'home_hero_image_1_upload' => UploadedFile::fake()->image('hero.jpg', 2400, 1400),
                'home_hero_image_1_crop' => json_encode([
                    'zoom' => 1.25,
                    'offsetX' => 40,
                    'offsetY' => -24,
                ]),
            ]);

        $response->assertRedirect(route('admin.settings'));

        $storedPath = Setting::query()->where('key', 'home_hero_image_1')->value('value');

        $this->assertNotNull($storedPath);
        $this->assertStringStartsWith('/storage/settings/headers/', $storedPath);
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $storedPath));
    }
}
