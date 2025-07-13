<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Report;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        // 20 random reports are created (on posts or comments)
        Report::factory()->count(20)->create();
    }
}
