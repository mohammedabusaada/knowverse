<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Generates a large synthetic dataset for load / performance benchmarking.
 *
 * Uses bulk inserts (DB::table) and deliberately bypasses Eloquent model events,
 * so seeding 100k+ rows does not trigger the observer cascade (reputation ledger,
 * notifications, activity log). The reputation ledger for these rows is left empty
 * on purpose — live ledger entries are created by the actual vote experiments.
 *
 * Run against your real MySQL database (NOT the SQLite test DB), e.g.:
 *   php artisan knowverse:seed-benchmark --fresh
 *   php artisan knowverse:seed-benchmark --users=1000 --posts=10000 --votes=100000
 */
class BenchmarkSeed extends Command
{
    protected $signature = 'knowverse:seed-benchmark
        {--users=500    : Number of synthetic scholars}
        {--posts=5000   : Number of discussions}
        {--comments=10000 : Number of comments}
        {--votes=50000  : Number of votes}
        {--tags=40      : Number of tags}
        {--fresh        : Wipe existing votes/comments/post_tags/posts/tags first}';

    protected $description = 'Generate a large synthetic dataset for load/performance benchmarking (bulk insert, no model events).';

    public function handle(): int
    {
        $nUsers    = max(1, (int) $this->option('users'));
        $nPosts    = max(0, (int) $this->option('posts'));
        $nComments = max(0, (int) $this->option('comments'));
        $nVotes    = max(0, (int) $this->option('votes'));
        $nTags     = max(0, (int) $this->option('tags'));

        $this->info('KnowVerse benchmark dataset');
        $this->line("  connection={$this->laravel['config']['database.default']}  users={$nUsers} posts={$nPosts} comments={$nComments} votes={$nVotes} tags={$nTags}");
        $started = microtime(true);

        // Roles must exist (idempotent, cross-DB).
        $this->callSilent('db:seed', ['--class' => \Database\Seeders\RoleSeeder::class, '--force' => true]);

        if ($this->option('fresh')) {
            $this->warn('Wiping existing content (votes, comments, post_tags, posts, tags)...');
            foreach (['votes', 'comments', 'post_tags', 'posts', 'tags'] as $t) {
                DB::table($t)->delete();
            }
        }

        $now = now()->format('Y-m-d H:i:s');

        // --- Users ---------------------------------------------------------
        $this->info('Seeding users...');
        $password = Hash::make('password'); // single hash, reused for every benchmark account
        $this->bulk('users', $nUsers, 2000, fn ($i) => [
            'username'            => 'bench_'.$i.'_'.Str::random(6),
            'email'               => 'bench_'.$i.'_'.Str::random(6).'@bench.local',
            'full_name'           => 'Bench Scholar '.$i,
            'password'            => $password,
            'role_id'             => 1,
            'reputation_points'   => 0,
            'public_follow_lists' => true,
            'email_verified_at'   => $now,
            'created_at'          => $now,
            'updated_at'          => $now,
        ]);
        $userIds = DB::table('users')->pluck('id')->all();

        // --- Tags ----------------------------------------------------------
        if ($nTags > 0) {
            $this->info('Seeding tags...');
            $this->bulk('tags', $nTags, 1000, fn ($i) => [
                'name'       => 'BenchTopic'.$i,
                'slug'       => 'benchtopic-'.$i,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
        $tagIds = DB::table('tags')->pluck('id')->all();

        // --- Posts ---------------------------------------------------------
        $this->info('Seeding posts...');
        $this->bulk('posts', $nPosts, 2000, fn ($i) => [
            'user_id'        => $userIds[array_rand($userIds)],
            'title'          => 'Benchmark discussion #'.$i.' on scholarly methodology',
            'body'           => 'This synthetic scholarly discussion examines methodology, peer validation, and reproducibility for benchmark iteration '.$i.'. It is long enough to exercise rendering, search, and pagination paths realistically.',
            'status'         => 'published',
            'is_hidden'      => 0,
            'view_count'     => random_int(0, 5000),
            'upvote_count'   => 0,
            'downvote_count' => 0,
            'created_at'     => $now,
            'updated_at'     => $now,
        ]);
        $postIds = DB::table('posts')->pluck('id')->all();

        // --- Post <-> Tag --------------------------------------------------
        if ($tagIds && $postIds) {
            $this->info('Linking posts to tags...');
            $rows = [];
            foreach ($postIds as $pid) {
                $k = random_int(1, min(3, count($tagIds)));
                foreach ((array) array_rand(array_flip($tagIds), $k) as $tid) {
                    $rows[] = ['post_id' => $pid, 'tag_id' => $tid, 'created_at' => $now];
                    if (count($rows) >= 2000) {
                        DB::table('post_tags')->insertOrIgnore($rows);
                        $rows = [];
                    }
                }
            }
            if ($rows) {
                DB::table('post_tags')->insertOrIgnore($rows);
            }
        }

        // --- Comments ------------------------------------------------------
        if ($nComments > 0 && $postIds) {
            $this->info('Seeding comments...');
            $this->bulk('comments', $nComments, 2000, fn ($i) => [
                'post_id'        => $postIds[array_rand($postIds)],
                'user_id'        => $userIds[array_rand($userIds)],
                'parent_id'      => null,
                'body'           => 'A synthetic scholarly comment contributing to benchmark discussion iteration '.$i.', written with enough length to render realistically.',
                'is_hidden'      => 0,
                'spam_score'     => 0,
                'upvote_count'   => 0,
                'downvote_count' => 0,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
        }
        $commentIds = DB::table('comments')->pluck('id')->all();

        // --- Votes (polymorphic; morph ALIASES match getMorphClass(); dedup) -
        if ($nVotes > 0) {
            $this->info('Seeding votes...');
            $rows = [];
            $made = 0;
            for ($i = 0; $i < $nVotes; $i++) {
                $onPost   = empty($commentIds) || random_int(1, 100) <= 60;
                $targetId = $onPost ? $postIds[array_rand($postIds)] : $commentIds[array_rand($commentIds)];
                $rows[] = [
                    'user_id'     => $userIds[array_rand($userIds)],
                    'target_type' => $onPost ? 'post' : 'comment', // alias, not FQCN
                    'target_id'   => $targetId,
                    'value'       => random_int(1, 100) <= 75 ? 1 : -1, // ~75% upvotes
                    'created_at'  => $now,
                ];
                if (count($rows) >= 2000) {
                    $made += DB::table('votes')->insertOrIgnore($rows);
                    $rows = [];
                }
            }
            if ($rows) {
                $made += DB::table('votes')->insertOrIgnore($rows);
            }
            $this->line("  inserted ~{$made} votes (duplicates on (user,target) skipped)");
        }

        // --- Recompute denormalised aggregates -----------------------------
        $this->info('Recomputing denormalised counters...');
        DB::statement("UPDATE posts SET
            upvote_count   = (SELECT COUNT(*) FROM votes WHERE votes.target_type='post' AND votes.target_id=posts.id AND votes.value=1),
            downvote_count = (SELECT COUNT(*) FROM votes WHERE votes.target_type='post' AND votes.target_id=posts.id AND votes.value=-1)");
        DB::statement("UPDATE comments SET
            upvote_count   = (SELECT COUNT(*) FROM votes WHERE votes.target_type='comment' AND votes.target_id=comments.id AND votes.value=1),
            downvote_count = (SELECT COUNT(*) FROM votes WHERE votes.target_type='comment' AND votes.target_id=comments.id AND votes.value=-1)");

        // Approximate reputation for realistic profile/leaderboard rendering.
        DB::statement("UPDATE users SET reputation_points = (
            COALESCE((SELECT SUM(p.upvote_count) * 5 + COUNT(p.id) * 5 FROM posts p WHERE p.user_id = users.id), 0)
            + COALESCE((SELECT SUM(c.upvote_count) * 2 + COUNT(c.id) * 2 FROM comments c WHERE c.user_id = users.id), 0)
        )");

        $elapsed = round(microtime(true) - $started, 1);
        $this->newLine();
        $this->info("Done in {$elapsed}s.");
        $this->table(['Table', 'Rows'], [
            ['users', number_format(DB::table('users')->count())],
            ['tags', number_format(DB::table('tags')->count())],
            ['posts', number_format(DB::table('posts')->count())],
            ['comments', number_format(DB::table('comments')->count())],
            ['votes', number_format(DB::table('votes')->count())],
        ]);
        $this->warn('Benchmark accounts: username "bench_<n>_<rand>", password "password".');

        return self::SUCCESS;
    }

    /**
     * Insert `$count` rows into `$table` in chunks of `$chunk`, building each row
     * from the `$row($index)` callback.
     */
    private function bulk(string $table, int $count, int $chunk, callable $row): void
    {
        if ($count <= 0) {
            return;
        }

        $bar = $this->output->createProgressBar($count);
        $buffer = [];
        for ($i = 0; $i < $count; $i++) {
            $buffer[] = $row($i);
            if (count($buffer) >= $chunk) {
                DB::table($table)->insert($buffer);
                $bar->advance(count($buffer));
                $buffer = [];
            }
        }
        if ($buffer) {
            DB::table($table)->insert($buffer);
            $bar->advance(count($buffer));
        }
        $bar->finish();
        $this->newLine();
    }
}
