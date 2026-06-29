<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('property_import_runs', function (Blueprint $table) {
            $table->string('payload_path')->nullable()->after('input_name');
            $table->unsignedInteger('total_properties')->default(0)->after('payload_path');
            $table->unsignedInteger('max_images_per_property')->default(12)->after('total_properties');
        });
    }

    public function down()
    {
        Schema::table('property_import_runs', function (Blueprint $table) {
            $table->dropColumn([
                'payload_path',
                'total_properties',
                'max_images_per_property',
            ]);
        });
    }
};
