<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\Zona;
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
        $response->assertSee(__('ui.home.latest_heading'));
        $response->assertSee('Ultima Publicada');
        $response->assertDontSee('Borrador Reciente');
    }

    public function test_home_search_uses_zones_and_types_from_published_catalogue(): void
    {
        $publishedZone = Zona::create(['nombre' => 'Alicante']);
        $draftOnlyZone = Zona::create(['nombre' => 'Zona borradores']);

        Property::factory()->create([
            'title' => 'Chalet publicado',
            'tipo' => 'Chalet',
            'zona_id' => $publishedZone->id,
            'status' => 'published',
        ]);
        Property::factory()->create([
            'title' => 'Castillo borrador',
            'tipo' => 'Castillo',
            'zona_id' => $draftOnlyZone->id,
            'status' => 'draft',
        ]);

        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Encuentra tu próxima vivienda')
            ->assertSee('Alicante (1)')
            ->assertSee('Chalet')
            ->assertDontSee('Zona borradores')
            ->assertDontSee('Castillo');
    }

    public function test_home_search_parameters_filter_property_results(): void
    {
        $alicante = Zona::create(['nombre' => 'Alicante']);
        $murcia = Zona::create(['nombre' => 'Murcia']);

        $matching = Property::factory()->create([
            'title' => 'Villa dentro del presupuesto',
            'tipo' => 'Villa',
            'zona_id' => $alicante->id,
            'price' => 240000,
            'status' => 'published',
        ]);
        Property::factory()->create([
            'title' => 'Villa demasiado cara',
            'tipo' => 'Villa',
            'zona_id' => $alicante->id,
            'price' => 640000,
            'status' => 'published',
        ]);
        Property::factory()->create([
            'title' => 'Villa en otra zona',
            'tipo' => 'Villa',
            'zona_id' => $murcia->id,
            'price' => 200000,
            'status' => 'published',
        ]);

        $this->get(route('guest.properties.index', [
            'zona' => [$alicante->id],
            'tipo' => ['Villa'],
            'precio_max' => 300000,
        ]))
            ->assertOk()
            ->assertSee($matching->title)
            ->assertDontSee('Villa demasiado cara')
            ->assertDontSee('Villa en otra zona');
    }
}
