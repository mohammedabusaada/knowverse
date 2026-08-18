<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            // Sanitised to satisfy the app's own `alpha_dash:ascii` username rule
            // (faker's userName() can contain dots/apostrophes); unique numeric suffix.
            'username'       => preg_replace('/[^a-z0-9_]/i', '_', $this->faker->userName()).'_'.$this->faker->unique()->numberBetween(1, 9_999_999),
            'email'          => $this->faker->unique()->safeEmail(),
            'full_name'      => $this->faker->name(),
            'academic_title' => $this->faker->randomElement(['Dr.', 'Prof.', 'Mr.', 'Ms.', null]),

            // Cryptographic hashing is handled automatically by the model's 'hashed' cast.
            'password' => 'password',

            // Default to a standard, verified scholar (role 1). Use the admin() /
            // moderator() / unverified() states below for privileged or pending accounts.
            'role_id'             => 1,
            'bio'                 => $this->faker->sentence(10),
            'profile_picture'     => null,
            'public_follow_lists' => true,
            'reputation_points'   => 0,
            'last_login_at'       => $this->faker->optional()->dateTimeThisYear(),
            'email_verified_at'   => now(),
            'remember_token'      => Str::random(10),
        ];
    }

    /**
     * Account whose email has not yet been verified.
     */
    public function unverified(): static
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }

    /**
     * System administrator (role 2).
     */
    public function admin(): static
    {
        return $this->state(fn () => ['role_id' => 2]);
    }

    /**
     * Community moderator (role 3).
     */
    public function moderator(): static
    {
        return $this->state(fn () => ['role_id' => 3]);
    }
}
