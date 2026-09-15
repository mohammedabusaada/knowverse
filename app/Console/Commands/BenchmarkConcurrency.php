<?php

namespace App\Console\Commands;

use App\Console\Commands\Concerns\SummarisesRuns;
use App\Models\Post;
use App\Models\Reputation;
use App\Models\User;
use App\Services\ReputationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

/**
 * Ledger consistency under genuinely concurrent writes.
 *
 * Launches parallel PHP processes against the configured MySQL/MariaDB database. The
 * processes wait at a common start barrier so their writes overlap, then:
 *
 *  A. Reversal race - every process repeatedly reverses the same user's awards for the
 *     same discussion. Exactly as many reversals as awards must result.
 *  B. Mixed vote load - every process casts, flips and retracts votes on a set of
 *     discussions created for the run.
 *
 * After each run the database is checked for: the ledger invariant of every affected
 * user; any ledger entry reversed more than once; any (user, action, source) with more
 * reversals than awards; vote counters that disagree with the votes table; and any
 * author whose net vote reputation differs from what the remaining votes imply.
 *
 * With --baseline, phase B uses the vote write path that preceded the atomic vote fix,
 * so the same load can be compared before and after.
 */
class BenchmarkConcurrency extends Command
{
    use SummarisesRuns;

    protected $signature = 'knowverse:benchmark-concurrency
        {--workers=8 : Parallel PHP processes}
        {--ops=300 : Vote operations per worker in phase B}
        {--awards=10 : Awards created for the reversal race in phase A}
        {--repeat=5 : Reversal attempts per worker in phase A}
        {--posts=20 : Discussions receiving the concurrent votes}
        {--voters=200 : Distinct voters used in phase B (fewer voters = more contention)}
        {--runs=1 : Repeat the experiment}
        {--baseline : Phase B uses the non-atomic vote write path that preceded the fix, for comparison}';

    protected $description = 'Ledger consistency under concurrent writes on MySQL/MariaDB (parallel processes).';

    /** First message seen for each worker error type (printed with -v). */
    private array $errorExamples = [];

    public function handle(): int
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            $this->error('This experiment needs MySQL or MariaDB: SQLite serialises all writes.');

            return self::FAILURE;
        }

        $workers = max(2, (int) $this->option('workers'));
        $ops = max(1, (int) $this->option('ops'));
        $awards = max(1, (int) $this->option('awards'));
        $repeat = max(1, (int) $this->option('repeat'));
        $postCount = max(1, (int) $this->option('posts'));
        $voterCount = max(2, (int) $this->option('voters'));
        $runs = max(1, (int) $this->option('runs'));

        $userIds = User::query()->inRandomOrder()->limit($postCount + $voterCount)->pluck('id')->all();
        if (count($userIds) < $postCount + $voterCount) {
            $this->error('Not enough users. Seed first:  php artisan knowverse:seed-benchmark');

            return self::FAILURE;
        }

        $this->info("Concurrency experiment on '".DB::connection()->getDatabaseName()."': {$workers} workers, {$runs} run(s)");
        $this->line("  phase A: {$awards} awards, {$workers} workers × {$repeat} reversal attempts");
        $baseline = (bool) $this->option('baseline');
        $this->line("  phase B: {$workers} workers × {$ops} vote operations on {$postCount} discussions by {$voterCount} voters"
            .($baseline ? ' (baseline: non-atomic vote path)' : ''));

        $rows = [];
        $throughputs = [];
        $csv = 'mode,workers,run,phase_a_awards,phase_a_reversals,phase_a_ok,phase_b_ops,phase_b_seconds,phase_b_ops_per_s,'
            ."worker_errors,invariant_violations,duplicate_reversals,over_reversed,counter_mismatches,vote_reputation_mismatches\n";
        $totalViolations = 0;

        for ($run = 1; $run <= $runs; $run++) {
            $this->line("  run {$run}/{$runs} ...");
            $r = $this->runOnce($run, $workers, $ops, $awards, $repeat, $postCount, $voterCount, $userIds, $baseline);

            $violations = (int) ! $r['phase_a_ok'] + $r['invariant'] + $r['duplicates'] + $r['over'] + $r['counters'] + $r['vote_rep'];
            $totalViolations += $violations;
            $throughputs[] = $r['ops_per_s'];
            $errorText = empty($r['errors']) ? '0' : implode('; ', array_map(fn ($k, $v) => "{$k}×{$v}", array_keys($r['errors']), $r['errors']));

            $rows[] = [
                $run,
                "{$r['reversals']}/{$awards} ".($r['phase_a_ok'] ? 'ok' : 'FAIL'),
                $r['ops'], $r['ops_per_s'], $errorText,
                $r['invariant'], $r['duplicates'], $r['over'], $r['counters'], $r['vote_rep'],
            ];
            $csv .= implode(',', [
                $baseline ? 'baseline' : 'atomic', $workers, $run, $awards, $r['reversals'], $r['phase_a_ok'] ? 1 : 0, $r['ops'], round($r['seconds'], 2), $r['ops_per_s'],
                '"'.str_replace('"', "'", $errorText).'"',
                $r['invariant'], $r['duplicates'], $r['over'], $r['counters'], $r['vote_rep'],
            ])."\n";
        }

        $this->newLine();
        $this->table(
            ['Run', 'A: reversals', 'B: ops', 'B: ops/s', 'Worker errors', 'Invariant', 'Dup. reversals', 'Over-reversed', 'Counters', 'Vote reputation'],
            $rows
        );
        if ($runs > 1) {
            $this->line('Phase B throughput: '.$this->formatSummary($this->summarise($throughputs)).' ops/s (mean ± SD).');
        }
        $this->writeCsv($baseline ? 'benchmark-concurrency-baseline.csv' : 'benchmark-concurrency.csv', $csv);

        if ($totalViolations > 0) {
            $this->error("Consistency violations detected: {$totalViolations}.");

            return self::FAILURE;
        }
        $this->info('No consistency violations in any run.');

        return self::SUCCESS;
    }

    private function runOnce(int $run, int $workers, int $ops, int $awards, int $repeat, int $postCount, int $voterCount, array $userIds, bool $baseline): array
    {
        config(['broadcasting.default' => 'null']);

        shuffle($userIds);
        $authorIds = array_slice($userIds, 0, $postCount);
        $voterIds = array_slice($userIds, $postCount, $voterCount);
        $involved = array_merge($authorIds, $voterIds);

        // Discussions inserted directly, so they start with no votes and no ledger rows.
        $insertPost = fn (int $authorId, string $name) => DB::table('posts')->insertGetId([
            'user_id' => $authorId,
            'title' => "Concurrency run {$run} {$name}",
            'body' => 'Synthetic discussion created by the concurrency experiment.',
            'status' => 'published',
            'is_hidden' => 0,
            'view_count' => 0,
            'upvote_count' => 0,
            'downvote_count' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $postIds = [];
        foreach ($authorIds as $i => $authorId) {
            $postIds[] = $insertPost($authorId, "discussion {$i}");
        }
        $racePostId = $insertPost($authorIds[0], 'reversal race');

        // Ledger-consistent baseline for every user involved.
        DB::table('users')->whereIn('id', $involved)->update([
            'reputation_points' => DB::raw('COALESCE((SELECT SUM(delta) FROM reputations WHERE reputations.user_id = users.id), 0)'),
        ]);

        // Phase A: concurrent reversal of the same user's awards for the same source. The
        // race uses its own discussion so that its outcome cannot affect the phase B checks.
        $raceUser = User::find($authorIds[0]);
        $racePost = Post::withoutGlobalScopes()->find($racePostId);
        $before = (int) $raceUser->reputation_points;
        $service = app(ReputationService::class);
        for ($i = 0; $i < $awards; $i++) {
            $service->award($raceUser, 'post_upvoted', null, $racePost);
        }
        $phaseA = $this->launch($workers, ['--mode=reverse', "--user={$raceUser->id}", "--post={$racePost->id}", "--repeat={$repeat}"]);
        $reversals = Reputation::where('user_id', $raceUser->id)
            ->where('action', 'post_upvoted_reverted')
            ->where('source_type', 'post')
            ->where('source_id', $racePost->id)
            ->count();
        $phaseAOk = $reversals === $awards && (int) $raceUser->fresh()->reputation_points === $before;

        // Phase B: mixed concurrent vote load.
        $phaseB = $this->launch($workers, array_merge(
            ['--mode=votes', "--ops={$ops}", '--voters='.implode(',', $voterIds), '--posts='.implode(',', $postIds)],
            $baseline ? ['--baseline'] : []
        ));

        $errors = $phaseA['errors'];
        foreach ($phaseB['errors'] as $k => $n) {
            $errors[$k] = ($errors[$k] ?? 0) + $n;
        }

        return array_merge($this->verify($involved, $postIds), [
            'reversals' => $reversals,
            'phase_a_ok' => $phaseAOk,
            'ops' => $phaseB['ops'],
            'seconds' => $phaseB['seconds'],
            'ops_per_s' => round($phaseB['ops'] / $phaseB['seconds'], 1),
            'errors' => $errors,
        ]);
    }

    /**
     * Start the workers together and collect their JSON reports.
     */
    private function launch(int $workers, array $args): array
    {
        $startAt = microtime(true) + 3.0; // common barrier after every process has booted
        $processes = [];
        for ($w = 0; $w < $workers; $w++) {
            $process = new Process(
                array_merge([PHP_BINARY, base_path('artisan'), 'knowverse:concurrency-worker', "--worker={$w}", '--start-at='.sprintf('%.6f', $startAt)], $args),
                base_path(), null, null, 900
            );
            $process->start();
            $processes[] = $process;
        }

        $result = ['ops' => 0, 'errors' => []];
        foreach ($processes as $process) {
            $process->wait();
            $report = null;
            foreach (array_reverse(explode("\n", $process->getOutput())) as $line) {
                if (str_starts_with(trim($line), '{')) {
                    $report = json_decode(trim($line), true);
                    break;
                }
            }

            if (! $process->isSuccessful() || ! is_array($report)) {
                $result['errors']['worker crashed'] = ($result['errors']['worker crashed'] ?? 0) + 1;
                $this->warn('  worker failed: '.substr(trim($process->getErrorOutput() ?: $process->getOutput()), 0, 300));

                continue;
            }

            $result['ops'] += $report['ops'];
            foreach ($report['errors'] as $k => $n) {
                $result['errors'][$k] = ($result['errors'][$k] ?? 0) + $n;
            }
            foreach ($report['examples'] ?? [] as $k => $message) {
                if (! isset($this->errorExamples[$k])) {
                    $this->errorExamples[$k] = $message;
                    $this->line("  <comment>{$k}</comment>: {$message}", null, 'v');
                }
            }
        }
        $result['seconds'] = max(0.001, microtime(true) - $startAt);

        return $result;
    }

    private function verify(array $involved, array $postIds): array
    {
        // 1. Ledger invariant for every user involved.
        $invariant = 0;
        foreach (array_chunk($involved, 500) as $chunk) {
            $points = DB::table('users')->whereIn('id', $chunk)->pluck('reputation_points', 'id');
            $sums = DB::table('reputations')->whereIn('user_id', $chunk)->groupBy('user_id')
                ->selectRaw('user_id, SUM(delta) AS total')->pluck('total', 'user_id');
            foreach ($points as $id => $value) {
                if ((int) $value !== (int) ($sums[$id] ?? 0)) {
                    $invariant++;
                }
            }
        }

        // 2. Any ledger entry reversed more than once (the unique index should make this 0).
        $duplicates = DB::query()->fromSub(
            DB::table('reputations')->select('reverses_id')->whereNotNull('reverses_id')->groupBy('reverses_id')->havingRaw('COUNT(*) > 1'),
            'd'
        )->count();

        // 3. Any (user, action, source) with more reversals than original awards.
        $over = DB::query()->fromSub(
            DB::table('reputations')->whereIn('user_id', $involved)
                ->selectRaw("user_id, source_type, source_id, REPLACE(action, '_reverted', '') AS base_action,
                    SUM(CASE WHEN RIGHT(action, 9) = '_reverted' THEN 0 ELSE 1 END) AS originals,
                    SUM(CASE WHEN RIGHT(action, 9) = '_reverted' THEN 1 ELSE 0 END) AS reversals")
                ->groupByRaw("user_id, source_type, source_id, REPLACE(action, '_reverted', '')"),
            'g'
        )->whereColumn('reversals', '>', 'originals')->count();

        // 4 and 5. Vote counters, and net vote reputation implied by the remaining votes.
        $counters = 0;
        $voteReputation = 0;
        $upPoints = (int) config('reputation.points.post_upvoted');
        $downPoints = (int) config('reputation.points.post_downvoted');
        foreach ($postIds as $postId) {
            $post = DB::table('posts')->where('id', $postId)->first(['user_id', 'upvote_count', 'downvote_count']);
            $votes = DB::table('votes')->where('target_type', 'post')->where('target_id', $postId);
            $up = (clone $votes)->where('value', 1)->count();
            $down = (clone $votes)->where('value', -1)->count();

            if ((int) $post->upvote_count !== $up || (int) $post->downvote_count !== $down) {
                $counters++;
            }

            $ledger = DB::table('reputations')->where('user_id', $post->user_id)
                ->where('source_type', 'post')->where('source_id', $postId);
            $netUp = (int) (clone $ledger)->whereIn('action', ['post_upvoted', 'post_upvoted_reverted'])->sum('delta');
            $netDown = (int) (clone $ledger)->whereIn('action', ['post_downvoted', 'post_downvoted_reverted'])->sum('delta');

            if ($netUp !== $up * $upPoints || $netDown !== $down * $downPoints) {
                $voteReputation++;
            }
        }

        return [
            'invariant' => $invariant,
            'duplicates' => $duplicates,
            'over' => $over,
            'counters' => $counters,
            'vote_rep' => $voteReputation,
        ];
    }

    private function writeCsv(string $name, string $content): void
    {
        $dir = storage_path('benchmarks');
        if (! is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        file_put_contents($dir.DIRECTORY_SEPARATOR.$name, $content);
        $this->info("CSV → storage/benchmarks/{$name}");
    }
}
