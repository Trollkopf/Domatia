<?php

namespace Tests\Feature;

use App\Models\KyeroFeed;
use App\Models\Property;
use App\Models\PropertyImportRun;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KyeroFeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_kyero_page_is_split_into_management_tabs(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.kyero.index'))
            ->assertOk()
            ->assertSee('Fuentes automáticas')
            ->assertSee('Importación manual')
            ->assertSee('Ejecuciones');
    }

    public function test_admin_can_save_a_recurring_kyero_feed(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.kyero.feeds.store'), [
            'name' => 'Feed nocturno',
            'url' => 'https://feeds.example.test/kyero.xml',
            'is_active' => '1',
            'max_images_per_property' => 8,
        ]);

        $response->assertRedirect(route('admin.kyero.index'));
        $this->assertDatabaseHas('kyero_feeds', [
            'name' => 'Feed nocturno',
            'is_active' => true,
            'max_images_per_property' => 8,
        ]);
    }

    public function test_scheduled_command_downloads_and_imports_active_feeds(): void
    {
        Storage::fake('local');
        Http::fake([
            'https://feeds.example.test/kyero.xml' => Http::response($this->feedXml(), 200, ['Content-Type' => 'application/xml']),
        ]);

        $feed = KyeroFeed::create([
            'name' => 'Feed nocturno',
            'url' => 'https://feeds.example.test/kyero.xml',
            'is_active' => true,
            'max_images_per_property' => 1,
        ]);

        $exitCode = Artisan::call('kyero:import-feeds');

        $this->assertSame(0, $exitCode);
        $this->assertSame('completed', $feed->fresh()->last_status);
        $this->assertNotNull($feed->fresh()->last_success_at);
        $this->assertSame(1, Property::query()->where('source_listing_id', 'AUTO-100')->count());
    }

    public function test_admin_can_trigger_a_saved_feed_manually(): void
    {
        Storage::fake('local');
        Http::fake([
            'https://feeds.example.test/kyero.xml' => Http::response($this->feedXml()),
        ]);

        $admin = User::factory()->create(['role' => 'admin']);
        $feed = KyeroFeed::create([
            'name' => 'Feed principal',
            'url' => 'https://feeds.example.test/kyero.xml',
            'is_active' => true,
            'max_images_per_property' => 3,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.kyero.feeds.run', $feed));
        $run = PropertyImportRun::query()->latest()->firstOrFail();

        $response->assertRedirect(route('admin.kyero.index', ['run' => $run->id]));
        $this->assertSame($feed->id, $run->kyero_feed_id);
        $this->assertSame('queued', $feed->fresh()->last_status);
    }

    private function feedXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<properties>
    <property>
        <id>AUTO-100</id>
        <ref>AUTO-100</ref>
        <price>250000</price>
        <type>Villa</type>
        <town>Torrevieja</town>
        <province>Alicante</province>
        <beds>3</beds>
        <baths>2</baths>
        <desc><es>Villa importada automáticamente.</es></desc>
    </property>
</properties>
XML;
    }
}
