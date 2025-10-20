<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed the essential roles table FIRST.
        // This ensures the foreign key constraint (role_id) is satisfied.
        $this->call([
            RoleSeeder::class,
        ]);

        // 2. You can enable other seeders here once they are ready.
        // Note: Any seeders that create Users (like UserSeeder) must run AFTER RoleSeeder.
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
