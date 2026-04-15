<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the application's database with a default admin user.
     *
     * @return void
     */
    public function run()
    {
        $adminGroup = UserGroup::query()->where('slug', 'admin')->first();

        User::updateOrCreate(
            ['email' => 'admin@domatia.test'],
            [
                'name' => 'Admin Domatia',
                'password' => Hash::make('admin1234'),
                'role' => $adminGroup?->slug ?? 'admin',
                'user_group_id' => $adminGroup?->id,
                'email_verified_at' => now(),
            ]
        );
    }
}
