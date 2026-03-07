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

        // Mute Model Events to prevent heavy observers (reputation/notifications) from firing
        Vote::withoutEvents(function () use ($users, $posts, $comments) {
            
            $votesToInsert = [];
            $now = now()->format('Y-m-d H:i:s');

            foreach ($users as $user) {
                // Each user votes on 5–15 posts/comments
                $targets = $posts->concat($comments)->shuffle()->take(rand(5, 15));

                foreach ($targets as $target) {
                    $votesToInsert[] = [
                        'user_id'     => $user->id,
                        'target_type' => get_class($target),
                        'target_id'   => $target->id,
                        'value'       => rand(0, 1) ? 1 : -1,
                        'created_at'  => $now,
                    ];
                }
            }

            // Bulk Insert into the database in chunks to avoid memory limits
            // Using insertOrIgnore prevents duplicate entry crashes smoothly.
            $chunks = array_chunk($votesToInsert, 500);
            foreach ($chunks as $chunk) {
                Vote::insertOrIgnore($chunk);
            }

        });
    }
}