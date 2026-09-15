<?php

namespace App\Console\Commands;

use App\Console\Commands\Concerns\SummarisesRuns;
use App\Models\Post;
use App\Models\Reputation;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Measures server-side vote-processing latency across the full cascade of
 * vote → reputation ledger → activity log → notification, then verifies the
 * ledger invariant (every user's reputation equals the sum of their ledger
 * deltas) over the affected accounts. With --runs=N the measurement is repeated
 * N times, the invariant is verified after every run, and each metric is
 * reported as mean ± SD with a 95% confidence interval.
 *
 * Network/WebSocket delivery is excluded here (measured separately by
 * knowverse:benchmark-ws); broadcasting is forced to the null driver so the
 * figure is the pure database cascade.
 */
class BenchmarkVotes extends Command
{
    use SummarisesRuns;

    protected $signature = 'knowverse:benchmark-votes
        {--samples=1000 : Number of vote operations to time per run}
        {--runs=1 : Repeat the measurement this many times and report mean ± SD}';

    protected $description = 'Server-side vote-processing latency + ledger consistency check (mean ± SD over repeated runs).';

    public function handle(): int
    {
        // Measure the DB/ledger cascade only — exclude the WebSocket round-trip.
        config(['broadcasting.default' => 'null']);

        $samples = max(1, (int) $this->option('samples'));
        $runs = max(1, (int) $this->option('runs'));

        $users = User::query()->inRandomOrder()->limit(500)->get();
        $posts = Post::query()->inRandomOrder()->limit(500)->get();

        if ($users->count() < 2 || $posts->isEmpty()) {
            $this->error('Insufficient data. Seed first:  php artisan knowverse:seed-benchmark');

            return self::FAILURE;
        }

        // Establish a ledger-consistent baseline ONCE. The bulk seeder sets reputation_points
        // to an approximate display value WITHOUT writing ledger rows; sync it to the ledger
        // truth so the runs measure whether VOTE PROCESSING preserves the invariant. It is
        // deliberately not repeated between runs, which would hide a violation.
        $this->info('Synchronising reputation baseline to the ledger...');
        DB::statement('UPDATE users SET reputation_points = COALESCE((SELECT SUM(delta) FROM reputations WHERE reputations.user_id = users.id), 0)');

        $perRun = [];
        $rawCsv = "run,operations,mean_ms,p50_ms,p95_ms,p99_ms,throughput_vps,ledger_violations\n";

        for ($run = 1; $run <= $runs; $run++) {
            $this->info("Run {$run}/{$runs}: timing {$samples} vote operations (server-side cascade, broadcasting disabled)...");
            [$latencies, $wall] = $this->timeVotes($samples, $users, $posts);
            sort($latencies);

            $this->info('Verifying ledger invariant...');
            $r = [
                'ops' => count($latencies),
                'mean' => round($this->mean($latencies), 2),
                'p50' => $this->pct($latencies, 50),
                'p95' => $this->pct($latencies, 95),
                'p99' => $this->pct($latencies, 99),
                'vps' => $wall > 0 ? round(count($latencies) / $wall, 1) : 0,
                'violations' => $this->countLedgerViolations(),
            ];
            $perRun[] = $r;
            $rawCsv .= implode(',', [$run, $r['ops'], $r['mean'], $r['p50'], $r['p95'], $r['p99'], $r['vps'], $r['violations']])."\n";
        }

        $summary = [];
        foreach (['mean', 'p50', 'p95', 'p99', 'vps'] as $metric) {
            $summary[$metric] = $this->summarise(array_column($perRun, $metric));
        }
        $violations = array_sum(array_column($perRun, 'violations'));

        $this->table(['Metric', $runs > 1 ? "Value (mean ± SD, {$runs} runs)" : 'Value'], [
            ['vote operations per run', $samples],
            ['mean latency (ms)', $this->formatSummary($summary['mean'], 2)],
            ['p50 latency (ms)', $this->formatSummary($summary['p50'], 2)],
            ['p95 latency (ms)', $this->formatSummary($summary['p95'], 2)],
            ['p99 latency (ms)', $this->formatSummary($summary['p99'], 2)],
            ['throughput (votes/s, single process)', $this->formatSummary($summary['vps'])],
            ['ledger invariant violations (all runs)', $violations],
        ]);

        $csv = "metric,runs,mean,sd,ci95\n"
            ."mean_ms,{$runs},".$this->csvSummary($summary['mean'])."\n"
            ."p50_ms,{$runs},".$this->csvSummary($summary['p50'])."\n"
            ."p95_ms,{$runs},".$this->csvSummary($summary['p95'])."\n"
            ."p99_ms,{$runs},".$this->csvSummary($summary['p99'])."\n"
            ."throughput_vps,{$runs},".$this->csvSummary($summary['vps'])."\n"
            ."ledger_violations_total,{$runs},{$violations},,\n";
        $this->writeCsv('benchmark-votes.csv', $csv);
        $this->writeCsv('benchmark-votes-runs.csv', $rawCsv);

        if ($violations > 0) {
            $this->error("Ledger invariant VIOLATED ({$violations} user-run(s)).");

            return self::FAILURE;
        }
        $this->info('Ledger invariant holds after every run (0 violations).');

        return self::SUCCESS;
    }

    /**
     * @return array{0: array<int, float>, 1: float} latencies in ms, and wall time in seconds
     */
    private function timeVotes(int $samples, Collection $users, Collection $posts): array
    {
        $bar = $this->output->createProgressBar($samples);
        $latencies = [];
        $start = microtime(true);
        $taken = 0;
        $guard = 0;

        while ($taken < $samples && $guard < $samples * 6) {
            $guard++;
            $post = $posts->random();
            $voter = $users->random();
            if ($voter->id === $post->user_id) {
                continue; // self-votes are no-ops
            }

            $t0 = hrtime(true);
            Vote::castVote($voter, $post, random_int(0, 1) ? 1 : -1);
            $latencies[] = (hrtime(true) - $t0) / 1e6; // ms

            $taken++;
            $bar->advance();
        }

        $wall = microtime(true) - $start;
        $bar->finish();
        $this->newLine(2);

        return [$latencies, $wall];
    }

    /**
     * Ledger consistency oracle: reputation_points must equal the sum of ledger deltas.
     */
    private function countLedgerViolations(): int
    {
        $violations = 0;
        User::query()->select('id', 'reputation_points')->chunkById(500, function ($chunk) use (&$violations) {
            foreach ($chunk as $u) {
                $sum = (int) Reputation::where('user_id', $u->id)->sum('delta');
                if ((int) $u->reputation_points !== $sum) {
                    $violations++;
                }
            }
        });

        return $violations;
    }

    private function pct(array $sorted, int $p): float
    {
        if (empty($sorted)) {
            return 0.0;
        }
        $i = (int) ceil($p / 100 * count($sorted)) - 1;

        return round($sorted[max(0, min($i, count($sorted) - 1))], 2);
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
