<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use App\Models\Zona;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnvironmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_zone_page_only_shows_published_properties(): void
    {
        $zona = Zona::create([
            'nombre' => 'Playa Flamenca',
            'slug' => 'playa-flamenca',
        ]);

        Property::factory()->create([
            'title' => 'Villa Publicada',
            'slug' => 'villa-publicada',
            'status' => 'published',
            'zona_id' => $zona->id,
        ]);

        Property::factory()->create([
            'title' => 'Villa Borrador',
            'slug' => 'villa-borrador',
            'status' => 'draft',
            'zona_id' => $zona->id,
        ]);

        $response = $this->get(route('zonas.show', $zona->slug));

        $response->assertOk();
        $response->assertSee('Villa Publicada');
        $response->assertDontSee('Villa Borrador');
    }

    public function test_zones_are_listed_alphabetically(): void
    {
        Zona::create(['nombre' => 'Zenia']);
        Zona::create(['nombre' => 'Alicante']);
        Zona::create(['nombre' => 'Murcia']);

        $this->get(route('environment'))
            ->assertOk()
            ->assertSeeInOrder(['Alicante', 'Murcia', 'Zenia']);
    }

    public function test_zone_without_image_uses_the_default_image(): void
    {
        $zona = Zona::create(['nombre' => 'Zona sin imagen']);
        $defaultImage = asset(Zona::DEFAULT_IMAGE);

        $this->assertSame($defaultImage, $zona->imageUrl());

        $this->get(route('environment'))
            ->assertOk()
            ->assertSee($defaultImage, false);

        $this->get(route('zonas.show', $zona->slug))
            ->assertOk()
            ->assertSee($defaultImage, false);
    }

    public function test_zone_without_custom_image_uses_a_property_photo(): void
    {
        $zona = Zona::create(['nombre' => 'Zona con propiedad']);
        Property::factory()->create([
            'zona_id' => $zona->id,
            'status' => 'published',
            'thumbnail' => 'properties/zona-cover.jpg',
        ]);

        $expectedImage = asset('storage/properties/zona-cover.jpg');

        $this->assertSame($expectedImage, $zona->fresh()->imageUrl());

        $this->get(route('environment'))
            ->assertOk()
            ->assertSee($expectedImage, false);
    }

    public function test_property_form_lists_zones_alphabetically(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Zona::create(['nombre' => 'Torrevieja']);
        Zona::create(['nombre' => 'Alicante']);
        Zona::create(['nombre' => 'Orihuela']);

        $this->actingAs($admin)
            ->get(route('admin.properties.create'))
            ->assertOk()
            ->assertSeeInOrder(['Alicante', 'Orihuela', 'Torrevieja']);
    }
}
