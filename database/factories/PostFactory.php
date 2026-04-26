<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $academicTopics = [
            'The Impact of Neural Networks on Modern Data Processing',
            'Ethical Implications of AI-Powered Content Moderation',
            'Evaluating Hybrid Referential Integrity in Distributed Databases',
            'Blockchain Solutions for Verifiable Academic Credentialing',
            'Cybersecurity Protocols in High-Traffic Educational Platforms',
            'The Role of Peer Review in Digital Scholarly Discourse'
        ];

        return [
            'user_id' => User::inRandomOrder()->value('id') ?? User::factory(),
            'title' => $this->faker->randomElement($academicTopics),
            'body' => "This scholarly discussion explores " . $this->faker->sentence() .
                " Our methodology focuses on " . $this->faker->paragraph(3) .
                " Preliminary results indicate a significant correlation between peer validation and content credibility.",
            'status' => $this->faker->randomElement(['draft', 'published', 'archived']),
            'is_hidden' => $this->faker->boolean(10),
            'view_count' => $this->faker->numberBetween(100, 5000),
            'upvote_count' => 0,
            'downvote_count' => 0,
            'created_at' => $this->faker->dateTimeThisYear(),
            'updated_at' => now(),
        ];
    }
}
