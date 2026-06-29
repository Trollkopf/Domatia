<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->timestamp('source_date')->nullable()->after('source_last_synced_at');
            $table->string('currency', 8)->nullable()->after('price');
            $table->string('price_freq', 32)->nullable()->after('currency');
            $table->boolean('part_ownership')->default(false)->after('price_freq');
            $table->boolean('leasehold')->default(false)->after('part_ownership');
            $table->boolean('new_build')->default(false)->after('leasehold');
            $table->string('town')->nullable()->after('location_ru');
            $table->string('province')->nullable()->after('town');
            $table->string('country')->nullable()->after('province');
            $table->string('location_detail')->nullable()->after('country');
            $table->decimal('latitude', 10, 7)->nullable()->after('location_detail');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->string('energy_consumption', 32)->nullable()->after('longitude');
            $table->string('energy_emissions', 32)->nullable()->after('energy_consumption');
            $table->string('video_url')->nullable()->after('energy_emissions');
            $table->string('virtual_tour_url')->nullable()->after('video_url');
            $table->text('source_notes')->nullable()->after('virtual_tour_url');
            $table->json('features_json')->nullable()->after('source_notes');
            $table->json('description_extra')->nullable()->after('features_json');
            $table->boolean('has_air_conditioning')->default(false)->after('tiene_piscina');
            $table->boolean('has_garage')->default(false)->after('has_air_conditioning');
            $table->boolean('has_lift')->default(false)->after('has_garage');
            $table->boolean('has_garden')->default(false)->after('has_lift');
            $table->boolean('has_terrace')->default(false)->after('has_garden');
            $table->boolean('has_sea_views')->default(false)->after('has_terrace');
            $table->boolean('has_parking')->default(false)->after('has_sea_views');
            $table->boolean('is_furnished')->default(false)->after('has_parking');
            $table->boolean('has_storage_room')->default(false)->after('is_furnished');
            $table->boolean('has_solarium')->default(false)->after('has_storage_room');
        });
    }

    public function down()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'source_date',
                'currency',
                'price_freq',
                'part_ownership',
                'leasehold',
                'new_build',
                'town',
                'province',
                'country',
                'location_detail',
                'latitude',
                'longitude',
                'energy_consumption',
                'energy_emissions',
                'video_url',
                'virtual_tour_url',
                'source_notes',
                'features_json',
                'description_extra',
                'has_air_conditioning',
                'has_garage',
                'has_lift',
                'has_garden',
                'has_terrace',
                'has_sea_views',
                'has_parking',
                'is_furnished',
                'has_storage_room',
                'has_solarium',
            ]);
        });
    }
};
