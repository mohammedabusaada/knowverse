<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            TagSeeder::class,
            PostSeeder::class,
            CommentSeeder::class,
            VotesSeeder::class,
            ReputationSeeder::class,
            NotificationsSeeder::class,
            UserActivitySeeder::class,
        ]);
    }
}
