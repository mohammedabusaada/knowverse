<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        // Weighted Role Distribution: Simulate a realistic hierarchy
        // (80% Standard Scholars, 10% System Admins, 10% Moderators)
        $roleId = $this->faker->randomElement([
            1, 1, 1, 1, 1, 1, 1, 1, // User
            2,                      // Admin
            3                       // Moderator
        ]);

        return [
            'username' => $this->faker->unique()->userName(),
            'email' => $this->faker->unique()->safeEmail(),
            'full_name' => $this->faker->name(),
            'academic_title' => $this->faker->randomElement(['Dr.', 'Prof.', 'Mr.', 'Ms.', null]),
            
            // Cryptographic hashing is automatically handled by the Model's 'hashed' cast
            'password' => 'password', 
            
            'role_id' => $roleId,
            'bio' => $this->faker->sentence(10),
            'profile_picture' => $this->faker->optional()->imageUrl(200, 200, 'people'),
            'reputation_points' => $this->faker->numberBetween(0, 500),
            'last_login_at' => $this->faker->optional()->dateTimeThisYear(),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }
}