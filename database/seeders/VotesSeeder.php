<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vote;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;

class VotesSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $posts = Post::all();
        $comments = Comment::all();

        foreach ($users as $user) {
            // Each user votes on 5–15 posts/comments
            $targets = $posts->concat($comments)->shuffle()->take(rand(5, 15));

            foreach ($targets as $target) {
                $value = rand(0, 1) ? 1 : -1;

                // Avoid duplicate votes
                Vote::firstOrCreate([
                    'user_id' => $user->id,
                    'target_type' => get_class($target),
                    'target_id' => $target->id,
                ], [
                    'value' => $value,
                    'created_at' => now()->format('Y-m-d H:i:s'),
                ]);
            }
        }
    }
}
