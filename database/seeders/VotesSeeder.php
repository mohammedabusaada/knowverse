<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VotesSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = User::pluck('id');
        $posts = Post::all();
        $comments = Comment::all();

        Vote::withoutEvents(function () use ($userIds, $posts, $comments) {

            // Generate votes for Posts
            foreach ($posts as $post) {
                $voterCount = rand(min(1, $userIds->count()), intval($userIds->count() * 0.8));
                $voters = $userIds->random($voterCount);

                foreach ($voters as $userId) {
                    Vote::updateOrCreate(
                        [
                            'user_id' => $userId,
                            'target_type' => $post->getMorphClass(),
                            'target_id' => $post->id,
                        ],
                        [
                            'value' => rand(1, 10) > 2 ? 1 : -1,
                            'created_at' => now(),
                        ]
                    );
                }
            }

            // Generate votes for Comments
            foreach ($comments as $comment) {
                $voterCount = rand(0, intval($userIds->count() * 0.5));
                if ($voterCount === 0) {
                    continue;
                }

                $voters = $userIds->random($voterCount);

                foreach ($voters as $userId) {
                    Vote::updateOrCreate(
                        [
                            'user_id' => $userId,
                            'target_type' => $comment->getMorphClass(),
                            'target_id' => $comment->id,
                        ],
                        [
                            'value' => rand(1, 10) > 2 ? 1 : -1,
                            'created_at' => now(),
                        ]
                    );
                }
            }
        });

        $postMorph = (new Post)->getMorphClass();
        $commentMorph = (new Comment)->getMorphClass();

        DB::statement("
            UPDATE posts p SET 
            upvote_count = (SELECT COUNT(*) FROM votes v WHERE v.target_id = p.id AND v.target_type = '{$postMorph}' AND v.value = 1),
            downvote_count = (SELECT COUNT(*) FROM votes v WHERE v.target_id = p.id AND v.target_type = '{$postMorph}' AND v.value = -1)
        ");

        DB::statement("
            UPDATE comments c SET 
            upvote_count = (SELECT COUNT(*) FROM votes v WHERE v.target_id = c.id AND v.target_type = '{$commentMorph}' AND v.value = 1),
            downvote_count = (SELECT COUNT(*) FROM votes v WHERE v.target_id = c.id AND v.target_type = '{$commentMorph}' AND v.value = -1)
        ");
    }
}
