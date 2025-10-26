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

        // Create a few fixed admin accounts for testing (not random)
        User::factory()->create([
            'username' => 'admin',
            'email' => 'admin@knowverse.test',
            'full_name' => 'System Administrator',
            'role_id' => Role::where('name', 'admin')->value('id'),
            'password' => 'admin1234', // will be hashed automatically
        ]);

        // Generate multiple random users (mix of user/admin by weight)
        User::factory(20)->create();
    }
}
