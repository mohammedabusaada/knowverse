<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Notification;

class NotificationsSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // Each user gets 5–10 notifications
            \App\Models\Notification::factory(rand(5, 10))->create([
                'user_id' => $user->id,
            ]);
        }
    }
}
