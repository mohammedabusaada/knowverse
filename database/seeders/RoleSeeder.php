<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable FK checks temporarily (safe for local dev)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('roles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Seed roles with fixed IDs to ensure consistent FK references
        Role::insert([
            [
                'id' => 1,
                'name' => 'user',
                'description' => 'Standard platform user with basic permissions.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'admin',
                'description' => 'System administrator with full control.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
