<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

// NOTE: This seeder is based on an older schema version.
// It will be updated to match the final database structure soon.
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Temporarily disabled old seeders.
        // $this->call([
        //     UserSeeder::class,
        //     TagSeeder::class,
        //     PostSeeder::class,
        //     CommentSeeder::class,
        //     NotificationSeeder::class,
        //     ReportSeeder::class,
        // ]);
    }
}
