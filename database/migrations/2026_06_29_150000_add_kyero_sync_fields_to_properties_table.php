<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('source_name')->nullable()->after('thumbnail');
            $table->string('source_listing_id')->nullable()->after('source_name');
            $table->string('source_payload_hash')->nullable()->after('source_listing_id');
            $table->timestamp('source_last_synced_at')->nullable()->after('source_payload_hash');

            $table->unique(['source_name', 'source_listing_id'], 'properties_source_unique');
        });
    }

    public function down()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropUnique('properties_source_unique');
            $table->dropColumn([
                'source_name',
                'source_listing_id',
                'source_payload_hash',
                'source_last_synced_at',
            ]);
        });
    }
};
