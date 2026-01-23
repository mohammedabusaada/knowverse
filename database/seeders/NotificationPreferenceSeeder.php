<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\NotificationPreference;

class NotificationPreferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
              $categories = config('notification-preferences.categories');

        User::chunk(100, function ($users) use ($categories) {
            foreach ($users as $user) {
                foreach ($categories as $type => $meta) {
                    NotificationPreference::firstOrCreate(
                        [
                            'user_id' => $user->id,
                            'type'    => $type,
                        ],
                        [
                            'enabled' => $meta['default'] ?? true,
                        ]
                    );
                }
            }
        });
    }
}
