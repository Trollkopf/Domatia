<?php

namespace Tests\Feature;

use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyFavoritesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_toggle_a_property_as_favorite(): void
    {
        $property = Property::factory()->create([
            'status' => 'published',
            'title' => 'Villa Favorita',
        ]);

        $response = $this->post(route('guest.property.favorite', $property->slug), [
            'redirect_to' => route('guest.property.show', $property->slug),
        ]);

        $response->assertRedirect(route('guest.property.show', $property->slug));
        $response->assertCookie('favorite_properties');
    }

    public function test_guest_can_toggle_a_property_as_favorite_via_json(): void
    {
        $property = Property::factory()->create([
            'status' => 'published',
            'title' => 'Villa Ajax',
        ]);

        $response = $this->postJson(route('guest.property.favorite', $property->slug), [
            'redirect_to' => route('guest.property.show', $property->slug),
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'is_favorite' => true,
            'property_slug' => $property->slug,
            'favorites_count' => 1,
        ]);
        $response->assertCookie('favorite_properties');
    }

    public function test_favorites_page_shows_saved_properties_from_cookie(): void
    {
        $savedProperty = Property::factory()->create([
            'status' => 'published',
            'title' => 'Casa Guardada',
            'slug' => 'casa-guardada',
        ]);

        Property::factory()->create([
            'status' => 'published',
            'title' => 'Otra Propiedad',
            'slug' => 'otra-propiedad',
        ]);

        $response = $this
            ->withUnencryptedCookie('favorite_properties', $savedProperty->slug)
            ->get(route('guest.properties.favorites'));

        $response->assertOk();
        $response->assertSee('Tus propiedades favoritas');
        $response->assertSee($savedProperty->title);
        $response->assertDontSee('Otra Propiedad');
    }

    public function test_draft_property_is_not_publicly_visible(): void
    {
        $draftProperty = Property::factory()->create([
            'status' => 'draft',
            'slug' => 'propiedad-borrador',
        ]);

        $this->get(route('guest.property.show', $draftProperty->slug))->assertNotFound();
    }
}
