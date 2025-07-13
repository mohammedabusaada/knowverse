<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        // For each user, create 3 to 6 notifications
        User::all()->each(function ($user) {
            Notification::factory()
                ->count(rand(3, 6))
                ->create([
                    'user_id' => $user->id,
                ]);
        });
    }
}
