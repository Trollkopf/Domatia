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
            $table->boolean('can_manage_properties')->default(false)->after('can_manage_settings');
            $table->boolean('can_manage_contacts')->default(false)->after('can_manage_properties');
            $table->boolean('can_manage_zonas')->default(false)->after('can_manage_contacts');
            $table->boolean('can_view_reports')->default(false)->after('can_manage_zonas');
        });

        DB::table('user_groups')->where('slug', 'admin')->update([
            'can_manage_properties' => true,
            'can_manage_contacts' => true,
            'can_manage_zonas' => true,
            'can_view_reports' => true,
            'can_access_backoffice' => true,
            'can_manage_users' => true,
            'can_manage_settings' => true,
        ]);

        DB::table('user_groups')->where('slug', 'moderator')->update([
            'can_manage_properties' => true,
            'can_manage_contacts' => false,
            'can_manage_zonas' => true,
            'can_view_reports' => true,
            'can_access_backoffice' => true,
            'can_manage_users' => false,
            'can_manage_settings' => false,
        ]);

        DB::table('user_groups')->where('slug', 'commercial')->update([
            'can_manage_properties' => true,
            'can_manage_contacts' => true,
            'can_manage_zonas' => false,
            'can_view_reports' => false,
            'can_access_backoffice' => true,
            'can_manage_users' => false,
            'can_manage_settings' => false,
        ]);

        DB::table('user_groups')->where('slug', 'user')->update([
            'can_manage_properties' => false,
            'can_manage_contacts' => false,
            'can_manage_zonas' => false,
            'can_view_reports' => false,
            'can_access_backoffice' => false,
            'can_manage_users' => false,
            'can_manage_settings' => false,
        ]);
    }

    public function down(): void
    {
        Schema::table('user_groups', function (Blueprint $table) {
            $table->dropColumn([
                'can_manage_properties',
                'can_manage_contacts',
                'can_manage_zonas',
                'can_view_reports',
            ]);
        });
    }
};
