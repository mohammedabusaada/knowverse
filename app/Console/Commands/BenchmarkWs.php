<?php

namespace App\Console\Commands;

use App\Console\Commands\Concerns\SummarisesRuns;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Broadcast;

/**
 * Broadcasts timestamped pings on the public "benchmark" channel.
 *
 *  - Measures app → Reverb publish latency directly (server side).
 *  - Drives the Node end-to-end probe: run benchmarks/ws-latency.mjs
 *    in another terminal to measure full event → client delivery latency.
 *
 * Requires:  BROADCAST_CONNECTION=reverb  and  `php artisan reverb:start` running.
 */
class BenchmarkWs extends Command
{
    use SummarisesRuns;

    protected $signature = 'knowverse:benchmark-ws
        {--count=100 : Number of broadcast events}
        {--interval=50 : Milliseconds between events}
        {--runs=1 : Repeat the broadcast batch this many times and report mean ± SD}';

    protected $description = 'Broadcast timestamped pings; measure app→Reverb publish latency and feed the end-to-end probe.';

    public function handle(): int
    {
        $count = max(1, (int) $this->option('count'));
        $interval = max(0, (int) $this->option('interval'));

        $driver = config('broadcasting.default');
        if (in_array($driver, ['null', 'log'], true)) {
            $this->warn("broadcasting.default is '{$driver}'. Set BROADCAST_CONNECTION=reverb and run `php artisan reverb:start`.");
        }

        $this->info("Broadcasting {$count} pings on public channel 'benchmark'...");
        $this->line('  (run `node benchmarks/ws-latency.mjs` in another terminal for end-to-end latency)');

        $runs = max(1, (int) $this->option('runs'));
        $perRun = [];
        $seq = 0;

        for ($run = 1; $run <= $runs; $run++) {
            if ($runs > 1) {
                $this->line("  run {$run}/{$runs}");
            }

            $publish = [];
            for ($i = 0; $i < $count; $i++) {
                $t0 = hrtime(true);
                try {
                    Broadcast::on('benchmark')
                        ->as('ping')
                        ->with(['seq' => $seq++, 't' => microtime(true)])
                        ->sendNow();
                } catch (\Throwable $e) {
                    $this->error('Broadcast failed: '.$e->getMessage());
                    $this->line('Is Reverb running?  php artisan reverb:start');

                    return self::FAILURE;
                }
                $publish[] = (hrtime(true) - $t0) / 1e6; // ms
                if ($interval > 0) {
                    usleep($interval * 1000);
                }
            }

            sort($publish);
            $perRun[] = ['p50' => $this->pct($publish, 50), 'p95' => $this->pct($publish, 95), 'p99' => $this->pct($publish, 99)];
        }

        $this->newLine();
        $this->table(['Metric (app → Reverb publish)', $runs > 1 ? "Value (ms, mean ± SD, {$runs} runs)" : 'Value (ms)'], [
            ['p50', $this->formatSummary($this->summarise(array_column($perRun, 'p50')), 2)],
            ['p95', $this->formatSummary($this->summarise(array_column($perRun, 'p95')), 2)],
            ['p99', $this->formatSummary($this->summarise(array_column($perRun, 'p99')), 2)],
        ]);
        $this->info('Read the Node probe output for end-to-end (event → client) delivery latency.');

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
}
