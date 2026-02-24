<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure roles exist before seeding users
        if (Role::count() === 0) {
            $this->call(RoleSeeder::class);
        }

        // 1. Create a fixed Admin account
        User::factory()->create([
            'username' => 'admin',
            'email' => 'admin@knowverse.test',
            'full_name' => 'System Administrator',
            'role_id' => 2, // Admin
            'password' => 'admin1234',
        ]);

        // 2. Create a fixed Moderator account
        User::factory()->create([
            'username' => 'moderator',
            'email' => 'moderator@knowverse.test',
            'full_name' => 'Community Moderator',
            'role_id' => 3, // Moderator
            'password' => 'moderator1234',
        ]);

        // Generate multiple random users (mix of user/admin/mod by weight)
        User::factory(20)->create();
    }
}