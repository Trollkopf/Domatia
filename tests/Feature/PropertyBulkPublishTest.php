<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyBulkPublishTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_bulk_publish_controls_in_property_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Property::factory()->count(2)->create(['status' => 'draft']);

        $response = $this->actingAs($admin)->get(route('admin.properties.index'));

        $response->assertOk()
            ->assertSee('Publicar seleccionados')
            ->assertSee('Publicar borradores filtrados')
            ->assertSee('data-property-select', false);
    }

    public function test_admin_can_publish_selected_drafts_in_bulk(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $drafts = Property::factory()->count(2)->create(['status' => 'draft']);
        $alreadyPublished = Property::factory()->create(['status' => 'published']);

        $response = $this->actingAs($admin)->patchJson(route('admin.properties.bulk-publish'), [
            'scope' => 'selected',
            'property_ids' => $drafts->pluck('id')->push($alreadyPublished->id)->all(),
        ]);

        $response->assertOk()
            ->assertJsonPath('updated_count', 2)
            ->assertJsonPath('success', true);

        $this->assertSame(0, Property::query()->whereIn('id', $drafts->pluck('id'))->where('status', 'draft')->count());
        $this->assertSame('published', $alreadyPublished->fresh()->status);
    }

    public function test_admin_can_publish_all_drafts_matching_current_filters(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Property::factory()->count(3)->create(['status' => 'draft', 'tipo' => 'villa']);
        $untouched = Property::factory()->create(['status' => 'draft', 'tipo' => 'piso']);

        $response = $this->actingAs($admin)->patchJson(route('admin.properties.bulk-publish'), [
            'scope' => 'filtered',
            'filters' => ['tipo' => 'Villa'],
        ]);

        $response->assertOk()->assertJsonPath('updated_count', 3);
        $this->assertSame(3, Property::query()->where('tipo', 'Villa')->where('status', 'published')->count());
        $this->assertSame('draft', $untouched->fresh()->status);
    }

    public function test_quick_status_update_returns_json_without_a_redirect(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $property = Property::factory()->create(['status' => 'draft']);

        $response = $this->actingAs($admin)->patchJson(route('admin.properties.quick-update', $property), [
            'status' => 'published',
        ]);

        $response->assertOk()
            ->assertJsonPath('property_id', $property->id)
            ->assertJsonPath('status', 'published')
            ->assertJsonPath('status_label', 'Publicada');

        $this->assertSame('published', $property->fresh()->status);
    }
}
