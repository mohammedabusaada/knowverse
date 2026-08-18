<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Seed the three fixed roles.
     *
     * Idempotent and cross-database (works on MySQL and the SQLite test DB).
     * Fixed IDs are required because the authorization layer references them
     * directly: 1 = user, 2 = admin, 3 = moderator.
     */
    public function run(): void
    {
        $roles = [
            ['id' => 1, 'name' => 'user',      'description' => 'Standard platform user with basic permissions.'],
            ['id' => 2, 'name' => 'admin',     'description' => 'System administrator with full control.'],
            ['id' => 3, 'name' => 'moderator', 'description' => 'Community moderator who handles reports and content.'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['id' => $role['id']],
                $role + ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
