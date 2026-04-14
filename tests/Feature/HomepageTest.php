<?php

namespace Tests\Feature;

use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_only_shows_published_featured_properties(): void
    {
        $visibleProperty = Property::factory()->create([
            'title' => 'Villa Visible',
            'is_featured' => true,
            'status' => 'published',
        ]);

        Property::factory()->create([
            'title' => 'Borrador Destacado',
            'is_featured' => true,
            'status' => 'draft',
        ]);

        Property::factory()->create([
            'title' => 'Publicado No Destacado',
            'is_featured' => false,
            'status' => 'published',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee($visibleProperty->title);
        $response->assertDontSee('Borrador Destacado');
        $response->assertSee('Propiedades destacadas');
    }

    public function test_homepage_shows_latest_published_properties_section(): void
    {
        Property::factory()->create([
            'title' => 'Ultima Publicada',
            'is_featured' => false,
            'status' => 'published',
        ]);

        Property::factory()->create([
            'title' => 'Borrador Reciente',
            'is_featured' => false,
            'status' => 'draft',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Ultimas propiedades incorporadas');
        $response->assertSee('Ultima Publicada');
        $response->assertDontSee('Borrador Reciente');
    }
}
