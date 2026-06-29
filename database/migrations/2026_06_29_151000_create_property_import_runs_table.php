<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('property_import_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('source_name');
            $table->string('status')->default('running');
            $table->string('input_name')->nullable();
            $table->unsignedInteger('properties_seen')->default(0);
            $table->unsignedInteger('properties_created')->default(0);
            $table->unsignedInteger('properties_updated')->default(0);
            $table->unsignedInteger('properties_skipped')->default(0);
            $table->unsignedInteger('images_downloaded')->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('property_import_runs');
    }
};
