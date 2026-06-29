<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KyeroImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_kyero_import_page(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.kyero.index'));

        $response->assertOk();
        $response->assertSee('Lanzar importacion de Kyero');
    }

    public function test_admin_can_import_properties_from_xml_file(): void
    {
        Storage::fake('public');
        Http::fake([
            'https://cdn.example.com/*' => Http::response('image-content', 200, ['Content-Type' => 'image/jpeg']),
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $xml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<properties>
    <property>
        <id>KY-1001</id>
        <reference>REF-1001</reference>
        <title>Villa con piscina</title>
        <description>Villa lista para entrar a vivir.</description>
        <town>Marbella</town>
        <region>Malaga</region>
        <zone>Milla de Oro</zone>
        <price>1250000</price>
        <type>villa</type>
        <bedrooms>4</bedrooms>
        <bathrooms>3</bathrooms>
        <surface_area>260</surface_area>
        <plot_area>820</plot_area>
        <pool>1</pool>
        <status>published</status>
        <images>
            <image>https://cdn.example.com/villa-1.jpg</image>
            <image>https://cdn.example.com/villa-2.jpg</image>
        </images>
    </property>
</properties>
XML;

        $file = UploadedFile::fake()->createWithContent('kyero.xml', $xml);

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.kyero.store'), [
                'xml_file' => $file,
            ]);

        $run = \App\Models\PropertyImportRun::query()->latest()->first();

        $response->assertRedirect(route('admin.kyero.index', ['run' => $run->id]));
        $response->assertSessionHas('success');

        while ($run->fresh()->status !== 'completed') {
            $this
                ->actingAs($admin)
                ->post(route('admin.kyero.process', $run));
        }

        $property = Property::query()->where('source_listing_id', 'KY-1001')->first();

        $this->assertNotNull($property);
        $this->assertSame('kyero', $property->source_name);
        $this->assertSame('Villa con piscina', $property->title);
        $this->assertSame('REF-1001', $property->ref);
        $this->assertSame('published', $property->status);
        $this->assertSame('Villa', $property->tipo);
        $this->assertSame(4, $property->bedrooms);
        $this->assertSame(3, $property->bathrooms);
        $this->assertSame(260, $property->area);
        $this->assertSame(820, $property->metros_solar);
        $this->assertTrue($property->tiene_piscina);
        $this->assertNotNull($property->thumbnail);
        $this->assertCount(1, $property->images);
        Storage::disk('public')->assertExists($property->thumbnail);
    }

    public function test_admin_can_import_kyero_feed_with_nested_image_urls_and_surface_area(): void
    {
        Storage::fake('public');
        Http::fake([
            'https://www.luxuryinvestments.eu/*' => Http::response('image-content', 200, ['Content-Type' => 'image/jpeg']),
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $xml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<root>
    <kyero>
        <feed_version>3</feed_version>
    </kyero>
    <property>
        <id>19223</id>
        <date>2026-05-31 21:45:43</date>
        <ref>R-1665</ref>
        <price>189950</price>
        <currency>EUR</currency>
        <price_freq>sale</price_freq>
        <new_build>1</new_build>
        <type>Apartment</type>
        <town>Playa Flamenca</town>
        <province>alicante</province>
        <country>spain</country>
        <location_detail>Residential resort</location_detail>
        <location>
            <latitude>37.9447</latitude>
            <longitude>-0.7312</longitude>
        </location>
        <beds>2</beds>
        <baths>2</baths>
        <surface_area>
            <built>78</built>
            <plot>0</plot>
        </surface_area>
        <desc>
            <en>Bright apartment with communal areas.</en>
            <es>Apartamento luminoso con zonas comunes.</es>
        </desc>
        <energy_rating>
            <consumption>B</consumption>
            <emissions>C</emissions>
        </energy_rating>
        <video_url>https://www.luxuryinvestments.eu/video-tour</video_url>
        <virtual_tour_url>https://www.luxuryinvestments.eu/virtual-tour</virtual_tour_url>
        <notes>Imported from source xml.</notes>
        <features>
            <feature>Air Conditioning</feature>
            <feature>Balcony</feature>
            <feature>Communal Swimming Pool</feature>
            <feature>Lift</feature>
        </features>
        <images>
            <image id="0">
                <url>https://www.luxuryinvestments.eu/wp-content/uploads/2026/05/PHOTO-1.jpg</url>
            </image>
            <image id="1">
                <url>https://www.luxuryinvestments.eu/wp-content/uploads/2026/05/PHOTO-2.jpg</url>
            </image>
        </images>
    </property>
</root>
XML;

        $file = UploadedFile::fake()->createWithContent('kyero-nested.xml', $xml);

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.kyero.store'), [
                'xml_file' => $file,
            ]);

        $run = \App\Models\PropertyImportRun::query()->latest()->first();

        $response->assertRedirect(route('admin.kyero.index', ['run' => $run->id]));
        $response->assertSessionHas('success');

        while ($run->fresh()->status !== 'completed') {
            $this
                ->actingAs($admin)
                ->post(route('admin.kyero.process', $run));
        }

        $property = Property::query()->where('source_listing_id', '19223')->first();

        $this->assertNotNull($property);
        $this->assertSame('Piso', $property->tipo);
        $this->assertSame('Playa Flamenca, alicante', $property->location);
        $this->assertSame('Apartamento luminoso con zonas comunes.', $property->description);
        $this->assertSame('Bright apartment with communal areas.', $property->description_en);
        $this->assertSame(78, $property->area);
        $this->assertSame('EUR', $property->currency);
        $this->assertSame('sale', $property->price_freq);
        $this->assertSame('Playa Flamenca', $property->town);
        $this->assertSame('alicante', $property->province);
        $this->assertSame('spain', $property->country);
        $this->assertSame('Residential resort', $property->location_detail);
        $this->assertSame(37.9447, $property->latitude);
        $this->assertSame(-0.7312, $property->longitude);
        $this->assertTrue($property->hasCoordinates());
        $this->assertFalse($property->tiene_solar);
        $this->assertTrue($property->tiene_patio);
        $this->assertTrue($property->tiene_piscina);
        $this->assertTrue($property->has_air_conditioning);
        $this->assertTrue($property->has_lift);
        $this->assertTrue($property->new_build);
        $this->assertSame('B', $property->energy_consumption);
        $this->assertSame('C', $property->energy_emissions);
        $this->assertSame('https://www.luxuryinvestments.eu/video-tour', $property->video_url);
        $this->assertSame('https://www.luxuryinvestments.eu/virtual-tour', $property->virtual_tour_url);
        $this->assertSame('Imported from source xml.', $property->source_notes);
        $this->assertContains('Air Conditioning', $property->featuresList());
        $this->assertContains('Lift', $property->featuresList());
        $this->assertNotNull($property->thumbnail);
        $this->assertCount(1, $property->images);
        Storage::disk('public')->assertExists($property->thumbnail);
    }

    public function test_import_builds_fallback_title_from_type_and_place_when_feed_has_no_title(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $xml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<properties>
    <property>
        <id>KY-2002</id>
        <ref>REF-2002</ref>
        <type>Villa</type>
        <town>Playa Flamenca</town>
        <price>320000</price>
        <status>published</status>
    </property>
</properties>
XML;

        $file = UploadedFile::fake()->createWithContent('kyero-no-title.xml', $xml);

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.kyero.store'), [
                'xml_file' => $file,
            ]);

        $run = \App\Models\PropertyImportRun::query()->latest()->first();

        $response->assertRedirect(route('admin.kyero.index', ['run' => $run->id]));

        while ($run->fresh()->status !== 'completed') {
            $this
                ->actingAs($admin)
                ->post(route('admin.kyero.process', $run));
        }

        $property = Property::query()->where('source_listing_id', 'KY-2002')->first();

        $this->assertNotNull($property);
        $this->assertSame('Villa en Playa Flamenca', $property->title);
        $this->assertNotSame('Propiedad importada', $property->title);
    }
}
