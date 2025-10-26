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
        // Fetch role IDs dynamically (in case of future expansion)
        $userRoleId = Role::where('name', 'user')->value('id') ?? 1;
        $adminRoleId = Role::where('name', 'admin')->value('id') ?? 2;

        // Weighted random role assignment: 90% user, 10% admin
        $roleId = $this->faker->boolean(10) ? $adminRoleId : $userRoleId;

        return [
            'username' => $this->faker->unique()->userName(),
            'email' => $this->faker->unique()->safeEmail(),
            'full_name' => $this->faker->name(),
            'academic_title' => $this->faker->randomElement(['Dr.', 'Prof.', 'Mr.', 'Ms.', null]),
            'password' => 'password', // hashed by model cast
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
