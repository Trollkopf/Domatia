<?php

namespace Database\Seeders;

use App\Models\User;
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
        User::updateOrCreate(
            ['email' => 'admin@domatia.test'],
            [
                'name' => 'Admin Domatia',
                'password' => Hash::make('admin1234'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
    }
}
