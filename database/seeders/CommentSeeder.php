<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\Comment;
use App\Models\User;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure posts exist
        if (Post::count() === 0) {
            $this->call(PostSeeder::class);
        }

        $users = User::all();
        $posts = Post::all();

        foreach ($posts as $post) {
            // 1. Create top-level comments first
            $topComments = Comment::factory(rand(2, 6))->create([
                'post_id' => $post->id,
                'user_id' => $users->random()->id,
                'parent_id' => null,
                'created_at' => now()->format('Y-m-d H:i:s'),
                'updated_at' => now()->format('Y-m-d H:i:s'),
            ]);

            // 2️. Create replies (nested comments)
            foreach ($topComments as $comment) {
                $replyCount = rand(0, 3); // 0–3 replies per top-level comment

                for ($i = 0; $i < $replyCount; $i++) {
                    Comment::factory()->create([
                        'post_id' => $post->id,
                        'user_id' => $users->random()->id,
                        'parent_id' => $comment->id, // guaranteed to exist
                        'created_at' => now()->format('Y-m-d H:i:s'),
                        'updated_at' => now()->format('Y-m-d H:i:s'),
                    ]);
                }
            }

            $randomComment = Comment::where('post_id', $post->id)->inRandomOrder()->first();
            // 3️. After creating the comments, select a random comment as the best comment.
            if ($randomComment) {
                $post->update(['best_comment_id' => $randomComment->id]);
            }
        }
    }
}
