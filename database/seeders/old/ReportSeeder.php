<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Report;

// NOTE: This seeder is based on an older schema version.
// It will be updated to match the final database structure soon.
class ReportSeeder extends Seeder
{
    public function run(): void
    {
        // 20 random reports are created (on posts or comments)
        Report::factory()->count(20)->create();
    }
}
