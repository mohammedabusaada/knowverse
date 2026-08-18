<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\Reputation;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Measures server-side vote-processing latency across the full cascade of
 * vote → reputation ledger → activity log → notification, then verifies the
 * ledger invariant (every user's reputation equals the sum of their ledger
 * deltas) over the affected accounts.
 *
 * Network/WebSocket delivery is excluded here (measured separately by
 * knowverse:benchmark-ws); broadcasting is forced to the null driver so the
 * figure is the pure database cascade.
 */
class BenchmarkVotes extends Command
{
    protected $signature = 'knowverse:benchmark-votes
        {--samples=1000 : Number of vote operations to time}';

    protected $description = 'Server-side vote-processing latency + ledger consistency check.';

    public function handle(): int
    {
        // Measure the DB/ledger cascade only — exclude the WebSocket round-trip.
        config(['broadcasting.default' => 'null']);

        $samples = max(1, (int) $this->option('samples'));

        $users = User::query()->inRandomOrder()->limit(500)->get();
        $posts = Post::query()->inRandomOrder()->limit(500)->get();

        if ($users->count() < 2 || $posts->isEmpty()) {
            $this->error('Insufficient data. Seed first:  php artisan knowverse:seed-benchmark');

            return self::FAILURE;
        }

        // Establish a ledger-consistent baseline. The bulk seeder sets reputation_points
        // to an approximate display value WITHOUT writing ledger rows; sync it to the
        // ledger truth so this run measures whether VOTE PROCESSING preserves the
        // invariant (not the seeder's shortcut).
        $this->info('Synchronising reputation baseline to the ledger...');
        DB::statement('UPDATE users SET reputation_points = COALESCE((SELECT SUM(delta) FROM reputations WHERE reputations.user_id = users.id), 0)');

        $this->info("Timing {$samples} vote operations (server-side cascade, broadcasting disabled)...");
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

        sort($latencies);
        $mean = round(array_sum($latencies) / max(1, count($latencies)), 2);

        // Ledger consistency oracle: reputation_points must equal the sum of ledger deltas.
        $this->info('Verifying ledger invariant...');
        $violations = 0;
        User::query()->select('id', 'reputation_points')->chunkById(500, function ($chunk) use (&$violations) {
            foreach ($chunk as $u) {
                $sum = (int) Reputation::where('user_id', $u->id)->sum('delta');
                if ((int) $u->reputation_points !== $sum) {
                    $violations++;
                }
            }
        });

        $this->table(['Metric', 'Value'], [
            ['vote operations', count($latencies)],
            ['mean latency (ms)', $mean],
            ['p50 latency (ms)', $this->pct($latencies, 50)],
            ['p95 latency (ms)', $this->pct($latencies, 95)],
            ['p99 latency (ms)', $this->pct($latencies, 99)],
            ['throughput (votes/s, single process)', $wall > 0 ? round(count($latencies) / $wall, 1) : 0],
            ['ledger invariant violations', $violations],
        ]);

        $csv = "metric,value\n"
            .'operations,'.count($latencies)."\n"
            ."mean_ms,{$mean}\n"
            .'p50_ms,'.$this->pct($latencies, 50)."\n"
            .'p95_ms,'.$this->pct($latencies, 95)."\n"
            .'p99_ms,'.$this->pct($latencies, 99)."\n"
            .'throughput_vps,'.($wall > 0 ? round(count($latencies) / $wall, 1) : 0)."\n"
            ."ledger_violations,{$violations}\n";
        $this->writeCsv('benchmark-votes.csv', $csv);

        if ($violations > 0) {
            $this->error("Ledger invariant VIOLATED for {$violations} user(s).");

            return self::FAILURE;
        }
        $this->info('Ledger invariant holds (0 violations).');

        return self::SUCCESS;
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
