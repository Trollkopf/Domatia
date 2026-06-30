<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\Zona;
use App\Models\ZonaSection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdditionalLocalesTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_locales_are_available_and_property_content_is_translated(): void
    {
        $property = Property::factory()->create([
            'status' => 'published',
            'title_nl' => 'Huis aan zee',
            'title_pl' => 'Dom nad morzem',
            'title_sv' => 'Hus vid havet',
            'title_da' => 'Hus ved havet',
            'description_nl' => 'Nederlandse beschrijving',
            'description_pl' => 'Polski opis',
            'description_sv' => 'Svensk beskrivning',
            'description_da' => 'Dansk beskrivelse',
        ]);

        foreach (['nl', 'pl', 'sv', 'da'] as $locale) {
            $this->withSession(['locale' => $locale])
                ->get(route('guest.property.show', $property->slug))
                ->assertOk()
                ->assertSee($property->{"title_{$locale}"})
                ->assertSee($property->{"description_{$locale}"});
        }
    }

    public function test_zone_and_section_support_new_locales(): void
    {
        $zone = Zona::create(['nombre' => 'Costa', 'nombre_nl' => 'Kust', 'nombre_pl' => 'Wybrzeże', 'nombre_sv' => 'Kust', 'nombre_da' => 'Kyst']);
        $section = ZonaSection::create(['zona_id' => $zone->id, 'titulo' => 'Vida', 'titulo_nl' => 'Leven', 'titulo_pl' => 'Życie', 'titulo_sv' => 'Liv', 'titulo_da' => 'Liv']);

        foreach (['nl', 'pl', 'sv', 'da'] as $locale) {
            $this->app->setLocale($locale);
            $this->assertSame($zone->{"nombre_{$locale}"}, $zone->translatedName());
            $this->assertSame($section->{"titulo_{$locale}"}, $section->translatedTitle());
        }
    }

    public function test_backoffice_property_form_contains_new_language_fields(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.properties.create'));
        $response->assertOk();
        foreach (['nl', 'pl', 'sv', 'da'] as $locale) {
            $response->assertSee('name="title_'.$locale.'"', false);
            $response->assertSee('name="description_'.$locale.'"', false);
            $response->assertSee('name="quick_summary_1_'.$locale.'"', false);
        }
    }
}
