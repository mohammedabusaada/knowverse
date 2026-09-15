<?php

namespace App\Console\Commands;

use App\Console\Commands\Concerns\SummarisesRuns;
use App\Models\Post;
use App\Models\Reputation;
use App\Models\User;
use App\Models\Vote;
use App\Services\ActivityService;
use App\Services\ReputationService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Ablation of the append-only reputation ledger against a plain counter.
 *
 *  A. Write-path cost - the same vote workload (casts, flips and retractions) is timed
 *     with reputation recorded in the ledger and with reputation kept only as a counter
 *     column. Everything else in the vote cascade is identical in both modes. Modes
 *     alternate run by run, and each mode votes on its own discussions.
 *  B. Detection and recovery - reputation counters of randomly chosen users are corrupted
 *     directly in the database. A counter alone offers no reference to compare against;
 *     with the ledger, a full scan must find exactly the corrupted users, and recalculation
 *     must restore their exact previous values.
 *
 * The bulk benchmark seeder writes no ledger rows. With --backfill the command first
 * derives the entries that the seeded posts, comments and votes would have produced, so
 * both parts run against a ledger of realistic size.
 */
class BenchmarkAblation extends Command
{
    use SummarisesRuns;

    protected $signature = 'knowverse:benchmark-ablation
        {--samples=1000 : Vote operations per run in part A}
        {--runs=5 : Runs per mode in part A, and repetitions of part B}
        {--corrupt=50 : Users whose counters are corrupted in each part B repetition}
        {--warmup=100 : Unmeasured vote operations per mode before part A}
        {--backfill : First derive the ledger entries implied by the seeded posts, comments and votes}';

    protected $description = 'Ledger vs plain counter: write-path cost, and detection/recovery of corrupted reputation.';

    public function handle(): int
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            $this->error('Run this against MySQL or MariaDB with a benchmark dataset.');

            return self::FAILURE;
        }

        config(['broadcasting.default' => 'null']);
        $samples = max(10, (int) $this->option('samples'));
        $runs = max(1, (int) $this->option('runs'));
        $corrupt = max(1, (int) $this->option('corrupt'));

        $this->info("Ablation on '".DB::connection()->getDatabaseName()."'");
        if ($this->option('backfill')) {
            $this->backfillLedger();
        }
        $this->syncCountersToLedger();

        $costOk = $this->partA($samples, $runs, max(0, (int) $this->option('warmup')));
        $this->syncCountersToLedger(); // counter mode deliberately left counters without ledger rows
        $recoveryOk = $this->partB($runs, $corrupt);

        return $costOk && $recoveryOk ? self::SUCCESS : self::FAILURE;
    }

    private function partA(int $samples, int $runs, int $warmup): bool
    {
        $this->newLine();
        $this->info("Part A: write-path cost, {$runs} runs × {$samples} vote operations per mode (modes alternate)");

        $voters = User::query()->inRandomOrder()->limit(200)->get()->all();
        $voterIds = array_map(fn (User $u) => $u->id, $voters);
        $posts = Post::withoutGlobalScopes()->whereNotIn('user_id', $voterIds)->inRandomOrder()->limit(100)->get()->all();
        $pools = ['ledger' => array_slice($posts, 0, 50), 'counter' => array_slice($posts, 50, 50)];

        $ledger = app(ReputationService::class);
        $counter = $this->counterOnlyService();
        $perRun = ['ledger' => [], 'counter' => []];
        $ledgerRowsBefore = Reputation::count();
        $rowsWritten = 0;
        $csv = "mode,run,operations,mean_ms,p50_ms,p95_ms,p99_ms,throughput_ops,ledger_rows_written\n";

        if ($warmup > 0) {
            foreach (['ledger' => $ledger, 'counter' => $counter] as $mode => $service) {
                app()->instance(ReputationService::class, $service);
                $this->timeVotes($warmup, $voters, $pools[$mode]);
            }
        }

        for ($run = 1; $run <= $runs; $run++) {
            foreach ($run % 2 ? ['ledger', 'counter'] : ['counter', 'ledger'] as $mode) {
                app()->instance(ReputationService::class, $mode === 'ledger' ? $ledger : $counter);

                $rows = Reputation::count();
                [$latencies, $wall] = $this->timeVotes($samples, $voters, $pools[$mode]);
                $written = Reputation::count() - $rows;
                if ($mode === 'ledger') {
                    $rowsWritten += $written;
                }

                sort($latencies);
                $r = [
                    'mean' => $this->mean($latencies),
                    'p50' => $this->pct($latencies, 50),
                    'p95' => $this->pct($latencies, 95),
                    'p99' => $this->pct($latencies, 99),
                    'ops' => count($latencies) / $wall,
                ];
                $perRun[$mode][] = $r;
                $csv .= implode(',', [$mode, $run, count($latencies), round($r['mean'], 3), round($r['p50'], 3), round($r['p95'], 3), round($r['p99'], 3), round($r['ops'], 1), $written])."\n";
                $this->line(sprintf('  run %d %-7s mean %.2f ms  p95 %.2f ms  %.1f ops/s  (%d ledger rows)', $run, $mode, $r['mean'], $r['p95'], $r['ops'], $written));
            }
        }
        app()->instance(ReputationService::class, $ledger);

        $rows = [];
        $summaryCsv = "metric,ledger_mean,ledger_sd,ledger_ci95,counter_mean,counter_sd,counter_ci95,overhead_pct\n";
        foreach (['mean' => 'mean latency (ms)', 'p50' => 'p50 latency (ms)', 'p95' => 'p95 latency (ms)', 'p99' => 'p99 latency (ms)', 'ops' => 'throughput (ops/s)'] as $key => $label) {
            $l = $this->summarise(array_column($perRun['ledger'], $key));
            $c = $this->summarise(array_column($perRun['counter'], $key));
            $overhead = $c['mean'] > 0 ? 100 * ($l['mean'] - $c['mean']) / $c['mean'] : 0;
            $rows[] = [$label, $this->formatSummary($l, 2), $this->formatSummary($c, 2), sprintf('%+.1f%%', $overhead)];
            $summaryCsv .= implode(',', [$key, $this->csvSummary($l, 3), $this->csvSummary($c, 3), round($overhead, 1)])."\n";
        }

        [$bytesPerRow, $totalRows] = $this->ledgerRowSize();
        $opsTotal = $samples * $runs;
        $rows[] = ['ledger rows per vote operation', round($rowsWritten / $opsTotal, 2), '0', ''];
        $rows[] = ['ledger storage per row (bytes, data + indexes)', $bytesPerRow, '0', "{$totalRows} rows"];
        $summaryCsv .= 'ledger_rows_per_op,'.round($rowsWritten / $opsTotal, 3).",,,0,,,\n";
        $summaryCsv .= "ledger_bytes_per_row,{$bytesPerRow},,,0,,,\n";

        $this->table(['Metric', 'Ledger (mean ± SD)', 'Counter only (mean ± SD)', 'Ledger overhead'], $rows);
        $this->writeCsv('benchmark-ablation-cost.csv', $summaryCsv);
        $this->writeCsv('benchmark-ablation-cost-runs.csv', $csv);
        $this->line("  (ledger rows before part A: {$ledgerRowsBefore})");

        return true;
    }

    private function partB(int $runs, int $corrupt): bool
    {
        $this->newLine();
        $this->info("Part B: detection and recovery, {$runs} repetitions × {$corrupt} corrupted users");

        $userCount = User::count();
        $initial = $this->scanViolations();
        if (count($initial) > 0) {
            $this->error('Counters are not consistent with the ledger before corruption: '.count($initial));

            return false;
        }

        $service = app(ReputationService::class);
        $allOk = true;
        $stats = ['scan' => [], 'repair' => []];
        $csv = "run,users,corrupted,detected,false_positives,missed,scan_ms,repaired_exactly,repair_ms,violations_after\n";

        for ($run = 1; $run <= $runs; $run++) {
            $victims = User::query()->inRandomOrder()->limit($corrupt)->pluck('reputation_points', 'id')->all();

            foreach (array_keys($victims) as $id) {
                $offset = random_int(1, 50) * (random_int(0, 1) ? 1 : -1);
                DB::table('users')->where('id', $id)->update(['reputation_points' => DB::raw("reputation_points + ({$offset})")]);
            }

            $t0 = hrtime(true);
            $detected = $this->scanViolations();
            $scanMs = (hrtime(true) - $t0) / 1e6;

            $falsePositives = count(array_diff($detected, array_keys($victims)));
            $missed = count(array_diff(array_keys($victims), $detected));

            $t0 = hrtime(true);
            foreach (User::whereIn('id', $detected)->get() as $user) {
                $service->recalc($user);
            }
            $repairMs = (hrtime(true) - $t0) / 1e6;

            $after = DB::table('users')->whereIn('id', array_keys($victims))->pluck('reputation_points', 'id')->all();
            $exact = count(array_filter(array_keys($victims), fn ($id) => (int) $after[$id] === (int) $victims[$id]));
            $remaining = count($this->scanViolations());

            $ok = $falsePositives === 0 && $missed === 0 && $exact === count($victims) && $remaining === 0;
            $allOk = $allOk && $ok;
            $stats['scan'][] = $scanMs;
            $stats['repair'][] = $repairMs;
            $csv .= implode(',', [$run, $userCount, count($victims), count($detected), $falsePositives, $missed, round($scanMs, 2), $exact, round($repairMs, 2), $remaining])."\n";
            $this->line(sprintf('  run %d: detected %d/%d, false positives %d, restored exactly %d/%d, violations after %d  (scan %.1f ms, repair %.1f ms)',
                $run, count($detected) - $falsePositives, count($victims), $falsePositives, $exact, count($victims), $remaining, $scanMs, $repairMs));
        }

        $this->table(['Metric', "Value ({$runs} repetitions)"], [
            ['users scanned per repetition', $userCount],
            ['ledger rows', Reputation::count()],
            ['full consistency scan (ms)', $this->formatSummary($this->summarise($stats['scan']), 2)],
            ["recalculation of {$corrupt} users (ms)", $this->formatSummary($this->summarise($stats['repair']), 2)],
            ['all corrupted users detected, none falsely, all restored exactly', $allOk ? 'yes, every repetition' : 'NO'],
        ]);
        $this->writeCsv('benchmark-ablation-recovery.csv', $csv);

        if (! $allOk) {
            $this->error('Detection or recovery was incomplete.');
        }

        return $allOk;
    }

    /**
     * The vote workload used by part A: 50% upvote, 30% downvote, 20% retract.
     *
     * @return array{0: array<int, float>, 1: float}
     */
    private function timeVotes(int $samples, array $voters, array $posts): array
    {
        $latencies = [];
        $start = microtime(true);

        for ($i = 0; $i < $samples; $i++) {
            $voter = $voters[array_rand($voters)];
            $post = $posts[array_rand($posts)];
            $roll = random_int(1, 100);

            $t0 = hrtime(true);
            if ($roll <= 50) {
                Vote::castVote($voter, $post, 1);
            } elseif ($roll <= 80) {
                Vote::castVote($voter, $post, -1);
            } else {
                Vote::retract($voter, $post);
            }
            $latencies[] = (hrtime(true) - $t0) / 1e6;
        }

        return [$latencies, max(0.001, microtime(true) - $start)];
    }

    /**
     * Reputation kept only as a counter column: no ledger row, no row lock, no lookup of
     * the entry being reversed. The activity log call is kept so that the two modes differ
     * only in the ledger.
     */
    private function counterOnlyService(): ReputationService
    {
        return new class extends ReputationService
        {
            public function award(User $user, string $action, ?int $customDelta = null, ?Model $source = null, ?string $note = null): Reputation
            {
                $delta = $customDelta ?? config("reputation.points.$action", 0);
                if ($delta !== 0) {
                    $user->increment('reputation_points', $delta);
                }
                ActivityService::reputationChanged($user, $delta, $source, $action);

                return new Reputation(['user_id' => $user->id, 'action' => $action, 'delta' => $delta]);
            }

            public function remove(User $user, string $action, ?Model $source = null): void
            {
                $delta = (int) config("reputation.points.$action", 0);
                if ($delta !== 0) {
                    $user->decrement('reputation_points', $delta);
                }
                ActivityService::reputationChanged($user, -$delta, $source, "{$action}_reverted");
            }
        };
    }

    /**
     * Users whose counter differs from the sum of their ledger entries.
     *
     * @return array<int, int>
     */
    private function scanViolations(): array
    {
        return DB::table('users')
            ->leftJoinSub(
                DB::table('reputations')->selectRaw('user_id, SUM(delta) AS total')->groupBy('user_id'),
                'ledger',
                'ledger.user_id', '=', 'users.id'
            )
            ->whereRaw('users.reputation_points <> COALESCE(ledger.total, 0)')
            ->pluck('users.id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    private function backfillLedger(): void
    {
        if (Reputation::exists()) {
            $this->warn('  Ledger is not empty; skipping backfill.');

            return;
        }

        $points = config('reputation.points');
        $t0 = microtime(true);

        DB::statement('INSERT INTO reputations (user_id, action, delta, source_type, source_id, created_at)
            SELECT user_id, ?, ?, ?, id, created_at FROM posts WHERE user_id IS NOT NULL', ['post_created', $points['post_created'], 'post']);
        DB::statement('INSERT INTO reputations (user_id, action, delta, source_type, source_id, created_at)
            SELECT user_id, ?, ?, ?, id, created_at FROM comments WHERE user_id IS NOT NULL', ['comment_created', $points['comment_created'], 'comment']);

        foreach (['post' => 'posts', 'comment' => 'comments'] as $type => $table) {
            DB::statement("INSERT INTO reputations (user_id, action, delta, source_type, source_id, created_at)
                SELECT t.user_id, IF(v.value = 1, ?, ?), IF(v.value = 1, ?, ?), ?, t.id, v.created_at
                FROM votes v JOIN {$table} t ON t.id = v.target_id
                WHERE v.target_type = ? AND t.user_id IS NOT NULL AND t.user_id <> v.user_id",
                ["{$type}_upvoted", "{$type}_downvoted", $points["{$type}_upvoted"], $points["{$type}_downvoted"], $type, $type]);
        }

        $this->line(sprintf('  Backfilled %s ledger entries in %.1f s.', number_format(Reputation::count()), microtime(true) - $t0));
    }

    private function syncCountersToLedger(): void
    {
        DB::statement('UPDATE users SET reputation_points = COALESCE((SELECT SUM(delta) FROM reputations WHERE reputations.user_id = users.id), 0)');
    }

    /**
     * @return array{0: int, 1: int} average bytes per ledger row (data + indexes), row count
     */
    private function ledgerRowSize(): array
    {
        DB::statement('ANALYZE TABLE reputations');
        $info = DB::selectOne('SELECT data_length + index_length AS bytes FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?', ['reputations']);
        $rows = Reputation::count();

        return [$rows > 0 ? (int) round($info->bytes / $rows) : 0, $rows];
    }

    private function pct(array $sorted, int $p): float
    {
        return $sorted[max(0, min(count($sorted) - 1, (int) ceil($p / 100 * count($sorted)) - 1))];
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
