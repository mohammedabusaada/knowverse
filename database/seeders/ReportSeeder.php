<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Report, User, Post, Comment};
use App\Enums\{ReportReason, ReportStatus};

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reporters = User::take(10)->get(); 
        $admins = User::whereHas('role', function($query) {
    $query->where('name', 'admin');
})->get();
        $posts = Post::take(10)->get();
        $comments = Comment::take(10)->get();

        // 1. Create "pending" reports on posts
        foreach ($posts->random(3) as $post) {
            Report::create([
                'reporter_id' => $reporters->random()->id,
                'target_id'   => $post->id,
                'target_type' => Post::class,
                'reason'      => 'This post contains misleading information.',
                'reason_type' => ReportReason::SPAM,
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
                'resolved_by' => $admins->random()->id ?? null,
                'resolved_at' => now(),
            ]);
        }
    }
}