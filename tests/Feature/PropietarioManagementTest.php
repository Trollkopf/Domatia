<?php

namespace Tests\Feature;

use App\Models\Propietario;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropietarioManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_and_create_property_owners(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.propietarios.index'))
            ->assertOk()
            ->assertSee('Nuevo propietario');

        $response = $this->actingAs($admin)->post(route('admin.propietarios.store'), [
            'nombre' => 'María López',
            'telefono' => '+34 600 123 456',
            'email' => 'maria@example.test',
            'notas' => 'Prefiere contacto por la mañana.',
        ]);

        $propietario = Propietario::query()->firstOrFail();
        $response->assertRedirect(route('admin.propietarios.edit', $propietario));
        $this->assertSame('maria@example.test', $propietario->email);
    }

    public function test_owner_search_returns_only_matching_results(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Propietario::create(['nombre' => 'Laura Sánchez', 'telefono' => '600111222']);
        Propietario::create(['nombre' => 'Miguel Torres', 'email' => 'miguel@example.test']);

        $response = $this->actingAs($admin)->getJson(route('admin.propietarios.search', ['q' => 'Laura']));

        $response->assertOk()
            ->assertJsonCount(1, 'results')
            ->assertJsonPath('results.0.label', 'Laura Sánchez');
    }

    public function test_property_form_uses_async_owner_search(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.properties.create'))
            ->assertOk()
            ->assertSee('Buscar por nombre, teléfono o email')
            ->assertSee(route('admin.propietarios.search'), false);
    }

    public function test_owner_page_lists_linked_properties(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $propietario = Propietario::create(['nombre' => 'Carlos Pérez']);
        $property = Property::factory()->create([
            'propietario_id' => $propietario->id,
            'title' => 'Villa del propietario',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.propietarios.edit', $propietario))
            ->assertOk()
            ->assertSee($property->title);
    }

    public function test_deleting_owner_keeps_property_and_clears_assignment(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $propietario = Propietario::create(['nombre' => 'Ana García']);
        $property = Property::factory()->create(['propietario_id' => $propietario->id]);

        $this->actingAs($admin)
            ->delete(route('admin.propietarios.destroy', $propietario))
            ->assertRedirect(route('admin.propietarios.index'));

        $this->assertDatabaseMissing('propietarios', ['id' => $propietario->id]);
        $this->assertNull($property->fresh()->propietario_id);
    }
}
