<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reputation;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;

class ReputationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $now = now()->format('Y-m-d H:i:s');

        Reputation::withoutEvents(function () use ($users, $now) {
            $reputationsToInsert = [];

            foreach ($users as $user) {
                // Generate 5–15 reputation events per user
                $eventsCount = rand(5, 15);

                for ($i = 0; $i < $eventsCount; $i++) {
                    $targetType = rand(0, 1) ? Post::class : Comment::class;
                    $target = $targetType::inRandomOrder()->first();

                    if (!$target) continue;

                    $action = $targetType === Post::class ? 'post_upvote' : 'comment_upvote';
                    
                    $reputationsToInsert[] = [
                        'user_id'     => $user->id,
                        'action'      => $action,
                        'delta'       => rand(1, 10),
                        'source_id'   => $target->id,
                        'source_type' => $targetType,
                        'note'        => 'Seeded event',
                        'created_at'  => $now,
                    ];
                }
            }

            foreach (array_chunk($reputationsToInsert, 500) as $chunk) {
                Reputation::insert($chunk);
            }
        });
    }
}