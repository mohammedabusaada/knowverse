<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

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
