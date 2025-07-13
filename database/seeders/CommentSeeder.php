<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\Post;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        // For each post, add 3‑7 major comments
        Post::all()->each(function ($post) {
            Comment::factory()
                ->count(rand(3, 7))
                ->create(['post_id' => $post->id])
                ->each(function ($comment) {
                    // For each comment, add 0‑2 responses
                    Comment::factory()
                        ->count(rand(0, 2))
                        ->create([
                            'post_id'  => $comment->post_id,
                            'parent_id' => $comment->id,
                        ]);
                });
        });
    }
}
