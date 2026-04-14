<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use App\Models\Zona;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_reports_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $zona = Zona::create([
            'nombre' => 'Centro',
            'imagen_principal' => 'zonas/centro.jpg',
        ]);

        $property = Property::factory()->create([
            'title' => 'Piso Centro',
            'status' => 'published',
            'is_featured' => true,
            'thumbnail' => 'properties/piso-centro.jpg',
            'zona_id' => $zona->id,
        ]);

        $property->contactos()->create([
            'nombre' => 'Ana Lead',
            'email' => 'ana@example.com',
            'telefono' => '600000000',
            'mensaje' => 'Quiero informacion',
            'status' => 'pendiente',
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.reports'));

        $response->assertOk();
        $response->assertSee('Embudo comercial');
        $response->assertSee('Propiedades con mas interes');
        $response->assertSee('Piso Centro');
    }

    public function test_non_admin_is_redirected_from_reports(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('admin.reports'));

        $response->assertRedirect('/');
    }

    public function test_reports_can_be_filtered_by_zone(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $zonaCentro = Zona::create([
            'nombre' => 'Centro',
            'imagen_principal' => 'zonas/centro.jpg',
        ]);

        $zonaCosta = Zona::create([
            'nombre' => 'Costa',
            'imagen_principal' => 'zonas/costa.jpg',
        ]);

        $propertyCentro = Property::factory()->create([
            'title' => 'Piso Centro',
            'status' => 'published',
            'zona_id' => $zonaCentro->id,
        ]);

        $propertyCosta = Property::factory()->create([
            'title' => 'Villa Costa',
            'status' => 'published',
            'zona_id' => $zonaCosta->id,
        ]);

        $propertyCentro->contactos()->create([
            'nombre' => 'Lead Centro',
            'email' => 'centro@example.com',
            'mensaje' => 'Info centro',
            'status' => 'pendiente',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $propertyCosta->contactos()->create([
            'nombre' => 'Lead Costa',
            'email' => 'costa@example.com',
            'mensaje' => 'Info costa',
            'status' => 'pendiente',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.reports', [
                'zona_id' => $zonaCentro->id,
                'from' => now()->subDay()->toDateString(),
                'to' => now()->toDateString(),
            ]));

        $response->assertOk();
        $response->assertSee('Piso Centro');
        $response->assertDontSee('Villa Costa');
    }
}
