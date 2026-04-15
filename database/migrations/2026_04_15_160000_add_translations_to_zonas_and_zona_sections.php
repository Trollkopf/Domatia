<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('zonas', function (Blueprint $table) {
            $table->string('nombre_en')->nullable()->after('nombre');
            $table->string('nombre_fr')->nullable()->after('nombre_en');
            $table->string('nombre_de')->nullable()->after('nombre_fr');
            $table->string('nombre_ru')->nullable()->after('nombre_de');
        });

        Schema::table('zona_sections', function (Blueprint $table) {
            $table->string('titulo_en')->nullable()->after('titulo');
            $table->string('titulo_fr')->nullable()->after('titulo_en');
            $table->string('titulo_de')->nullable()->after('titulo_fr');
            $table->string('titulo_ru')->nullable()->after('titulo_de');
            $table->text('descripcion_en')->nullable()->after('descripcion');
            $table->text('descripcion_fr')->nullable()->after('descripcion_en');
            $table->text('descripcion_de')->nullable()->after('descripcion_fr');
            $table->text('descripcion_ru')->nullable()->after('descripcion_de');
        });
    }

    public function down(): void
    {
        Schema::table('zona_sections', function (Blueprint $table) {
            $table->dropColumn([
                'titulo_en',
                'titulo_fr',
                'titulo_de',
                'titulo_ru',
                'descripcion_en',
                'descripcion_fr',
                'descripcion_de',
                'descripcion_ru',
            ]);
        });

        Schema::table('zonas', function (Blueprint $table) {
            $table->dropColumn([
                'nombre_en',
                'nombre_fr',
                'nombre_de',
                'nombre_ru',
            ]);
        });
    }
};
