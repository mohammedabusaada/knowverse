<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;

// NOTE: This seeder is based on an older schema version.
// It will be updated to match the final database structure soon.
class TagSeeder extends Seeder
{
    public function run(): void
    {
        Tag::factory()->count(10)->create();
    }
}
