<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('can_access_backoffice')->default(false);
            $table->boolean('can_manage_users')->default(false);
            $table->boolean('can_manage_settings')->default(false);
            $table->timestamps();
        });

        $now = now();

        DB::table('user_groups')->insert([
            [
                'name' => 'Administradores',
                'slug' => 'admin',
                'description' => 'Acceso total al backoffice, usuarios y ajustes.',
                'can_access_backoffice' => true,
                'can_manage_users' => true,
                'can_manage_settings' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Moderadores',
                'slug' => 'moderator',
                'description' => 'Acceso operativo al backoffice para revisar contenido y actividad.',
                'can_access_backoffice' => true,
                'can_manage_users' => false,
                'can_manage_settings' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Comerciales',
                'slug' => 'commercial',
                'description' => 'Acceso comercial para trabajar propiedades y contactos.',
                'can_access_backoffice' => true,
                'can_manage_users' => false,
                'can_manage_settings' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Usuarios',
                'slug' => 'user',
                'description' => 'Cuenta sin acceso al backoffice.',
                'can_access_backoffice' => false,
                'can_manage_users' => false,
                'can_manage_settings' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('user_group_id')->nullable()->after('role')->constrained('user_groups')->nullOnDelete();
        });

        $groupIds = DB::table('user_groups')->pluck('id', 'slug');

        DB::table('users')
            ->select(['id', 'role'])
            ->orderBy('id')
            ->get()
            ->each(function ($user) use ($groupIds) {
                $groupSlug = match ($user->role) {
                    'admin', 'moderator', 'commercial', 'user' => $user->role,
                    default => 'user',
                };

                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'role' => $groupSlug,
                        'user_group_id' => $groupIds[$groupSlug] ?? $groupIds['user'],
                    ]);
            });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_group_id');
        });

        Schema::dropIfExists('user_groups');
    }
};
