<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_groups', function (Blueprint $table) {
            $table->boolean('can_publish_properties')->default(false)->after('can_manage_properties');
            $table->boolean('can_export_reports')->default(false)->after('can_view_reports');
        });

        DB::table('user_groups')->where('slug', 'admin')->update([
            'can_publish_properties' => true,
            'can_export_reports' => true,
        ]);

        DB::table('user_groups')->where('slug', 'moderator')->update([
            'can_publish_properties' => true,
            'can_export_reports' => true,
        ]);

        DB::table('user_groups')->where('slug', 'commercial')->update([
            'can_publish_properties' => false,
            'can_export_reports' => false,
        ]);

        DB::table('user_groups')->where('slug', 'user')->update([
            'can_publish_properties' => false,
            'can_export_reports' => false,
        ]);
    }

    public function down(): void
    {
        Schema::table('user_groups', function (Blueprint $table) {
            $table->dropColumn([
                'can_publish_properties',
                'can_export_reports',
            ]);
        });
    }
};
