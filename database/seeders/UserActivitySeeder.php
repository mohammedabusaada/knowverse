<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserActivity;

class UserActivitySeeder extends Seeder
{
    public function run(): void
    {
        User::all()->each(function ($user) {
            UserActivity::factory(rand(5, 15))->create([
                'user_id' => $user->id,
            ]);
        });
    }
}

