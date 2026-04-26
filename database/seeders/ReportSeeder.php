<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Report, User, Post, Comment};
use App\Enums\{ReportReason, ReportStatus};

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        // Retrieve necessary entities for logical associations [cite: 323, 324]
        $reporters = User::take(10)->get();
        $moderators = User::whereIn('role_id', [2, 3])->get();
        $posts = Post::take(10)->get();
        $comments = Comment::take(10)->get();

        // Select specific users to simulate targeted administrative reports [cite: 325]
        $reportedUsers = User::where('role_id', 1)->inRandomOrder()->take(3)->get();

        // 1. Create "Pending" reports on academic discussions (Posts) [cite: 326]
        foreach ($posts->random(3) as $post) {
            Report::create([
                'reporter_id' => $reporters->random()->id,
                'target_id'   => $post->id,
                'target_type' => Post::class,
                'reason'      => 'This contribution lacks proper academic citations and includes unverified claims.',
                'reason_type' => ReportReason::MISINFORMATION,
                'status'      => ReportStatus::PENDING,
            ]);
        }

        // 2. Create "Resolved" reports on scholarly interactions (Comments) [cite: 328]
        foreach ($comments->random(3) as $comment) {
            Report::create([
                'reporter_id' => $reporters->random()->id,
                'target_id'   => $comment->id,
                'target_type' => Comment::class,
                'reason'      => 'The user is utilizing non-scholarly language and violating the community discourse guidelines.',
                'reason_type' => ReportReason::HATE_SPEECH,
                'status'      => ReportStatus::RESOLVED,
                'resolved_by' => $moderators->random()->id ?? null,
                'resolved_at' => now(),
            ]);
        }

        // 3. Create "Pending" reports on Users for policy violations [cite: 331]
        foreach ($reportedUsers as $reportedUser) {
            Report::create([
                'reporter_id' => $reporters->random()->id,
                'target_id'   => $reportedUser->id,
                'target_type' => User::class,
                'reason'      => 'This account is engaged in systematic spamming and artificial reputation farming.',
                'reason_type' => ReportReason::HARASSMENT,
                'status'      => ReportStatus::PENDING,
            ]);
        }
    }
}
