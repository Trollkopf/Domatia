<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('quick_summary_1')->nullable()->after('tiene_piscina');
            $table->string('quick_summary_2')->nullable()->after('quick_summary_1');
            $table->string('quick_summary_3')->nullable()->after('quick_summary_2');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'quick_summary_1',
                'quick_summary_2',
                'quick_summary_3',
            ]);
        });
    }
};
