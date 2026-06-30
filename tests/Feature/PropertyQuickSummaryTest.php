<?php

namespace Tests\Feature;

use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class PropertyQuickSummaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_summary_uses_customer_facing_availability_copy(): void
    {
        App::setLocale('es');
        $property = Property::factory()->create([
            'status' => 'published',
            'ref' => 'SWD5243',
            'quick_summary_3' => null,
        ]);

        $availability = $property->quickSummaryItems()[2];

        $this->assertSame('Disponible. Solicita más información o concierta una visita sin compromiso.', $availability);
        $this->assertStringNotContainsString('backoffice', $availability);
        $this->assertStringNotContainsString('SWD5243', $availability);
        $this->assertSame('Disponibilidad', __('ui.properties.summary_labels.operation'));
    }
}
