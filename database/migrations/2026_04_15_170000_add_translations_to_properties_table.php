<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('title_en')->nullable()->after('title');
            $table->string('title_fr')->nullable()->after('title_en');
            $table->string('title_de')->nullable()->after('title_fr');
            $table->string('title_ru')->nullable()->after('title_de');

            $table->string('location_en')->nullable()->after('location');
            $table->string('location_fr')->nullable()->after('location_en');
            $table->string('location_de')->nullable()->after('location_fr');
            $table->string('location_ru')->nullable()->after('location_de');

            $table->string('quick_summary_1_en')->nullable()->after('quick_summary_1');
            $table->string('quick_summary_1_fr')->nullable()->after('quick_summary_1_en');
            $table->string('quick_summary_1_de')->nullable()->after('quick_summary_1_fr');
            $table->string('quick_summary_1_ru')->nullable()->after('quick_summary_1_de');

            $table->string('quick_summary_2_en')->nullable()->after('quick_summary_2');
            $table->string('quick_summary_2_fr')->nullable()->after('quick_summary_2_en');
            $table->string('quick_summary_2_de')->nullable()->after('quick_summary_2_fr');
            $table->string('quick_summary_2_ru')->nullable()->after('quick_summary_2_de');

            $table->string('quick_summary_3_en')->nullable()->after('quick_summary_3');
            $table->string('quick_summary_3_fr')->nullable()->after('quick_summary_3_en');
            $table->string('quick_summary_3_de')->nullable()->after('quick_summary_3_fr');
            $table->string('quick_summary_3_ru')->nullable()->after('quick_summary_3_de');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'title_en',
                'title_fr',
                'title_de',
                'title_ru',
                'location_en',
                'location_fr',
                'location_de',
                'location_ru',
                'quick_summary_1_en',
                'quick_summary_1_fr',
                'quick_summary_1_de',
                'quick_summary_1_ru',
                'quick_summary_2_en',
                'quick_summary_2_fr',
                'quick_summary_2_de',
                'quick_summary_2_ru',
                'quick_summary_3_en',
                'quick_summary_3_fr',
                'quick_summary_3_de',
                'quick_summary_3_ru',
            ]);
        });
    }
};
