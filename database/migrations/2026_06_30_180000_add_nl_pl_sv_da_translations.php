<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $locales = ['nl', 'pl', 'sv', 'da'];

    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            foreach ($this->locales as $locale) {
                $table->string("title_{$locale}")->nullable();
                $table->string("location_{$locale}")->nullable();
                $table->text("description_{$locale}")->nullable();
                foreach ([1, 2, 3] as $index) {
                    $table->string("quick_summary_{$index}_{$locale}")->nullable();
                }
            }
        });

        Schema::table('zonas', function (Blueprint $table) {
            foreach ($this->locales as $locale) {
                $table->string("nombre_{$locale}")->nullable();
            }
        });

        Schema::table('zona_sections', function (Blueprint $table) {
            foreach ($this->locales as $locale) {
                $table->string("titulo_{$locale}")->nullable();
                $table->text("descripcion_{$locale}")->nullable();
            }
        });

        DB::table('properties')->whereNotNull('description_extra')->orderBy('id')->each(function ($property): void {
            $extra = json_decode($property->description_extra, true);
            if (! is_array($extra)) {
                return;
            }

            $updates = [];
            foreach ($this->locales as $locale) {
                if (filled($extra[$locale] ?? null)) {
                    $updates["description_{$locale}"] = $extra[$locale];
                    unset($extra[$locale]);
                }
            }
            if ($updates !== []) {
                $updates['description_extra'] = $extra !== [] ? json_encode($extra, JSON_UNESCAPED_UNICODE) : null;
                DB::table('properties')->where('id', $property->id)->update($updates);
            }
        });
    }

    public function down(): void
    {
        Schema::table('zona_sections', function (Blueprint $table) {
            $columns = [];
            foreach ($this->locales as $locale) {
                $columns[] = "titulo_{$locale}";
                $columns[] = "descripcion_{$locale}";
            }
            $table->dropColumn($columns);
        });

        Schema::table('zonas', function (Blueprint $table) {
            $table->dropColumn(array_map(fn ($locale) => "nombre_{$locale}", $this->locales));
        });

        Schema::table('properties', function (Blueprint $table) {
            $columns = [];
            foreach ($this->locales as $locale) {
                $columns = [...$columns, "title_{$locale}", "location_{$locale}", "description_{$locale}"];
                foreach ([1, 2, 3] as $index) {
                    $columns[] = "quick_summary_{$index}_{$locale}";
                }
            }
            $table->dropColumn($columns);
        });
    }
};
