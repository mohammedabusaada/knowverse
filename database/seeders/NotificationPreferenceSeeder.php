<?php

namespace Database\Seeders;

use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Database\Seeder;

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
                            'type' => $type,
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
