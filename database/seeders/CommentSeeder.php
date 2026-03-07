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

        // Mute Model Events (Observers) to bypass heavy logic 
        // like Reputation, Activities, and Notifications during database seeding.
        Comment::withoutEvents(function () use ($users, $posts) {
            Post::withoutEvents(function () use ($users, $posts) {
                
                foreach ($posts as $post) {
                    // 1. Create top-level comments
                    $topComments = Comment::factory(rand(2, 6))->create([
                        'post_id' => $post->id,
                        'user_id' => $users->random()->id,
                        'parent_id' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Store created comments in memory to avoid DB queries later
                    $allCommentsForPost = collect($topComments);

                    // 2. Create replies (nested comments)
                    foreach ($topComments as $comment) {
                        $replyCount = rand(0, 3);

                        if ($replyCount > 0) {
                            $replies = Comment::factory($replyCount)->create([
                                'post_id' => $post->id,
                                'user_id' => $users->random()->id,
                                'parent_id' => $comment->id,
                                'is_hidden'  => false,
                                'spam_score' => 0,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                            
                            $allCommentsForPost = $allCommentsForPost->merge($replies);
                        }
                    }

                    // 3. OPTIMIZATION: Pick randomly from the Collection in memory,
                    // avoiding the heavily expensive "ORDER BY RAND()" DB query.
                    if ($allCommentsForPost->isNotEmpty()) {
                        $post->update(['best_comment_id' => $allCommentsForPost->random()->id]);
                    }
                }

            });
        });
    }
}