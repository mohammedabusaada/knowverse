<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            TagSeeder::class,
            PostSeeder::class,
            CommentSeeder::class,
            VotesSeeder::class,
            NotificationsSeeder::class,
            UserActivitySeeder::class,
            NotificationPreferenceSeeder::class,
            ReportSeeder::class,
        ]);

        // ==========================================================
        // After all bulk data is inserted silently, we must calculate
        // the true reputation points for every scholar based on their
        // generated content and received votes.
        // ==========================================================

        $users = User::withCount([
            'posts',
            'allComments',
        ])->get();

        foreach ($users as $user) {
            $totalPoints = 0;

            // 1. Points from creating content
            $totalPoints += ($user->posts_count * config('reputation.points.post_created', 5));
            $totalPoints += ($user->all_comments_count * config('reputation.points.comment_created', 2));

            // 2. Points from received votes on their content
            // Summing the aggregate columns directly from the user's content.
            $postUpvotes = $user->posts()->sum('upvote_count');
            $commentUpvotes = $user->allComments()->sum('upvote_count');
            $postDownvotes = $user->posts()->sum('downvote_count');

            // Apply configuration point weights
            $totalPoints += ($postUpvotes * config('reputation.points.post_upvoted', 2));
            $totalPoints += ($commentUpvotes * config('reputation.points.comment_upvoted', 1));

            // Subtract downvotes (Negative Reputation is ALLOWED based on our strategy)
            $totalPoints -= ($postDownvotes * abs(config('reputation.points.post_downvoted', 1)));

            // Persist the mathematically accurate score to the User model
            User::withoutEvents(function () use ($user, $totalPoints) {
                $user->update(['reputation_points' => $totalPoints]);
            });
        }
    }
}
