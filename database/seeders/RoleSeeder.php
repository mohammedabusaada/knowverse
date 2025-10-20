<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Using raw SQL or truncate/unguard is often necessary when manually
        // setting IDs, which is necessary here to ensure the 'user' role is ID 1.

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Role::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create the roles, ensuring 'user' is the first one inserted.
        // The first insert will typically receive ID 1, which matches the hidden
        // input value of '1' we used in the registration form.
        Role::create([
            'id' => 1,
            'name' => 'user',
            'description' => 'Standard platform user with basic permissions.',
        ]);

        Role::create([
            'id' => 2,
            'name' => 'admin',
            'description' => 'System administrator with full control.',
        ]);

        // Other roles here...
    }
}
