<?php

namespace Tests\Feature;

use App\Models\Property;
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
}
