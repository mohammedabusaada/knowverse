<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
{
    $scholarlyComments = [
        'I appreciate the rigorous methodology applied in this research.',
        'Could you elaborate more on the security protocols mentioned in the third section?',
        'This contribution significantly clarifies the gap in current scholarly platforms.',
        'I have cross-referenced these citations and found the data to be highly accurate.',
        'Have you considered the scalability implications of this database design?'
    ];

    return [
        'post_id' => Post::inRandomOrder()->value('id') ?? Post::factory(),
        'user_id' => User::inRandomOrder()->value('id') ?? User::factory(),
        'parent_id' => null, 
        'body' => $this->faker->randomElement($scholarlyComments),
        'is_hidden' => $this->faker->boolean(5),
        'spam_score' => $this->faker->numberBetween(0, 1),
        'upvote_count' => 0,
        'downvote_count' => 0,
        'created_at' => $this->faker->dateTimeThisYear(),
        'updated_at' => now(),
    ];
}
}