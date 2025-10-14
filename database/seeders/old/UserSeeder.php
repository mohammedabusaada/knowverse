<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

// NOTE: This seeder is based on an older schema version.
// It will be updated to match the final database structure soon.
class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->count(10)->create();
    }
}
