<?php

namespace Database\Seeders;

use App\Models\PropertyImage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Property;
use Illuminate\Support\Str;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Property::factory()->count(6)->create();
        $properties = Property::factory()->count(5)->create();

foreach ($properties as $property) {
    for ($i = 1; $i <= rand(3, 5); $i++) {
        PropertyImage::create([
            'property_id' => $property->id,
            'url' => 'https://placehold.co/600x400?text=Img+' . $i,
            'path' => 'properties/seed-image-' . $i . '.jpg',
        ]);
    }
}
    }
}
