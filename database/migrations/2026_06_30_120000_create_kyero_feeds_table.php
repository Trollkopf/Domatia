<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kyero_feeds', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('url');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('max_images_per_property')->default(12);
            $table->foreignId('last_import_run_id')->nullable()->constrained('property_import_runs')->nullOnDelete();
            $table->string('last_status')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('last_success_at')->nullable();
            $table->timestamps();
        });

        Schema::table('property_import_runs', function (Blueprint $table) {
            $table->foreignId('kyero_feed_id')->nullable()->after('user_id')->constrained('kyero_feeds')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('property_import_runs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kyero_feed_id');
        });

        Schema::dropIfExists('kyero_feeds');
    }
};
