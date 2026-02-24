<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Report, User, Post, Comment};
use App\Enums\{ReportReason, ReportStatus};

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $reporters = User::take(10)->get(); 
        $moderators = User::whereIn('role_id', [2, 3])->get();
        $posts = Post::take(10)->get();
        $comments = Comment::take(10)->get();
        // Get normal users to be reported
        $reportedUsers = User::where('role_id', 1)->inRandomOrder()->take(3)->get();

        // 1. Create "pending" reports on posts
        foreach ($posts->random(3) as $post) {
            Report::create([
                'reporter_id' => $reporters->random()->id,
                'target_id'   => $post->id,
                'target_type' => Post::class,
                'reason'      => 'This post contains misleading information.',
                'reason_type' => ReportReason::MISINFORMATION,
                'status'      => ReportStatus::PENDING,
            ]);
        }

        // 2. Create "resolved" reports on comments
        foreach ($comments->random(3) as $comment) {
            Report::create([
                'reporter_id' => $reporters->random()->id,
                'target_id'   => $comment->id,
                'target_type' => Comment::class,
                'reason'      => 'Hate speech in comments.',
                'reason_type' => ReportReason::HATE_SPEECH,
                'status'      => ReportStatus::RESOLVED,
                'resolved_by' => $moderators->random()->id ?? null,
                'resolved_at' => now(),
            ]);
        }

        // 3. Create "pending" reports on Users (For banning tests)
        foreach ($reportedUsers as $reportedUser) {
            Report::create([
                'reporter_id' => $reporters->random()->id,
                'target_id'   => $reportedUser->id,
                'target_type' => User::class,
                'reason'      => 'This user is spamming the forum and harassing others.',
                'reason_type' => ReportReason::HARASSMENT,
                'status'      => ReportStatus::PENDING,
            ]);
        }
    }
}