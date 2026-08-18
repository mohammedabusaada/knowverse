<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Database\Seeder;

class UserActivitySeeder extends Seeder
{
    public function run(): void
    {
        UserActivity::withoutEvents(function () {
            User::all()->each(function ($user) {
                UserActivity::factory(rand(5, 15))->create([
                    'user_id' => $user->id,
                ]);
            });
        });
    }
}
