<?php

namespace App\Console\Commands;

use App\Console\Commands\Concerns\SummarisesRuns;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\TransferStats;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Concurrent HTTP load test over the public read endpoints. Produces response-time
 * percentiles, throughput, and error rate per (endpoint, concurrency) cell. With
 * --runs=N the whole grid is repeated N times — sweeping every cell in each run so
 * that drift is spread across cells — and each metric is reported as mean ± SD with
 * a 95% confidence interval.
 *
 * Prerequisites: the app must be serving (e.g. `php artisan serve`) with a seeded DB.
 *   php artisan knowverse:seed-benchmark --fresh
 *   php artisan serve            # in another terminal
 *   php artisan knowverse:benchmark-http --runs=5
 */
class BenchmarkHttp extends Command
{
    use SummarisesRuns;

    protected $signature = 'knowverse:benchmark-http
        {--base=http://127.0.0.1:8000 : Base URL of the running app}
        {--requests=500 : Requests per (endpoint, concurrency) cell}
        {--concurrency=10,50,100,200 : Comma-separated concurrency levels}
        {--endpoints=home,posts,show,search : Endpoints (home,posts,show,search)}
        {--runs=1 : Repeat the whole grid this many times and report mean ± SD}
        {--warmup=3 : Warm-up requests per endpoint before measuring}';

    protected $description = 'Concurrent HTTP load test → response-time percentiles, throughput, error rate (mean ± SD over repeated runs).';

    public function handle(): int
    {
        $base = rtrim($this->option('base'), '/');
        $perCell = max(1, (int) $this->option('requests'));
        $runs = max(1, (int) $this->option('runs'));
        $warmup = max(0, (int) $this->option('warmup'));
        $concLevels = array_filter(array_map('intval', explode(',', $this->option('concurrency'))));
        $endpointKeys = array_map('trim', explode(',', $this->option('endpoints')));

        $postId = DB::table('posts')->where('status', 'published')->where('is_hidden', false)->value('id')
            ?? DB::table('posts')->value('id');

        $paths = [
            'home' => '/',
            'posts' => '/posts',
            'show' => $postId ? "/posts/{$postId}" : null,
            'search' => '/search?q=benchmark',
        ];

        // Connectivity check.
        try {
            (new Client(['timeout' => 5]))->get($base.'/', ['http_errors' => false]);
        } catch (\Throwable $e) {
            $this->error("Cannot reach {$base}. Start the app first:  php artisan serve");

            return self::FAILURE;
        }

        $cells = [];
        foreach ($endpointKeys as $key) {
            $path = $paths[$key] ?? null;
            if ($path === null) {
                $this->warn("Skipping unknown/unavailable endpoint '{$key}'");

                continue;
            }
            foreach ($concLevels as $conc) {
                $cells[] = [$key, $conc, $base.$path];
            }
        }

        if (empty($cells)) {
            $this->error('No endpoints to test.');

            return self::FAILURE;
        }

        if ($perCell < max($concLevels)) {
            $this->warn("Requests per cell ({$perCell}) is below the highest concurrency (".max($concLevels).'); that level can never be reached.');
        }

        if ($warmup > 0) {
            $this->line("Warming up ({$warmup} requests per endpoint)...");
            $client = new Client(['timeout' => 60]);
            foreach (array_unique(array_column($cells, 2)) as $url) {
                for ($i = 0; $i < $warmup; $i++) {
                    try {
                        $client->get($url, ['http_errors' => false]);
                    } catch (\Throwable) {
                        // Warm-up failures are not measured.
                    }
                }
            }
        }

        $this->info("Load testing {$base}  ({$perCell} req/cell, {$runs} run(s))");

        $results = [];
        $rawCsv = "run,endpoint,concurrency,p50_ms,p95_ms,p99_ms,throughput_rps,error_pct\n";
        for ($run = 1; $run <= $runs; $run++) {
            foreach ($cells as [$key, $conc, $url]) {
                $this->line("  run {$run}/{$runs}  {$key} @ concurrency {$conc} ...");
                $r = $this->runCell($url, $perCell, $conc);
                $results["{$key}|{$conc}"][] = $r;
                $rawCsv .= implode(',', [$run, $key, $conc, $r['p50'], $r['p95'], $r['p99'], $r['rps'], $r['err']])."\n";
            }
        }

        $rows = [];
        $csv = 'endpoint,concurrency,runs,'
            .'p50_mean_ms,p50_sd_ms,p50_ci95_ms,p95_mean_ms,p95_sd_ms,p95_ci95_ms,'
            .'p99_mean_ms,p99_sd_ms,p99_ci95_ms,throughput_mean_rps,throughput_sd_rps,throughput_ci95_rps,'
            ."error_pct_mean,error_pct_max\n";

        foreach ($cells as [$key, $conc]) {
            $cell = $results["{$key}|{$conc}"];
            $summary = [];
            foreach (['p50', 'p95', 'p99', 'rps'] as $metric) {
                $summary[$metric] = $this->summarise(array_column($cell, $metric));
            }
            $errors = array_column($cell, 'err');

            $rows[] = [
                $key, $conc,
                $this->formatSummary($summary['p50']),
                $this->formatSummary($summary['p95']),
                $this->formatSummary($summary['p99']),
                $this->formatSummary($summary['rps']),
                round($this->mean($errors), 2),
            ];

            $csv .= implode(',', [
                $key, $conc, $runs,
                $this->csvSummary($summary['p50']),
                $this->csvSummary($summary['p95']),
                $this->csvSummary($summary['p99']),
                $this->csvSummary($summary['rps']),
                round($this->mean($errors), 2),
                max($errors),
            ])."\n";
        }

        $this->newLine();
        $this->table(['Endpoint', 'Conc', 'p50 ms', 'p95 ms', 'p99 ms', 'req/s', 'err %'], $rows);
        if ($runs > 1) {
            $this->line('Values are mean ± sample SD across runs; 95% confidence intervals are in the CSV.');
        }

        $this->writeCsv('benchmark-http.csv', $csv);
        $this->writeCsv('benchmark-http-runs.csv', $rawCsv);

        return self::SUCCESS;
    }

    private function runCell(string $url, int $total, int $concurrency): array
    {
        $client = new Client(['timeout' => 30]);
        $latencies = [];
        $errors = 0;

        $requests = function () use ($total, $url) {
            for ($i = 0; $i < $total; $i++) {
                yield new Request('GET', $url);
            }
        };

        $start = microtime(true);
        (new Pool($client, $requests(), [
            'concurrency' => $concurrency,
            'options' => [
                'http_errors' => false,
                'on_stats' => function (TransferStats $s) use (&$latencies) {
                    $t = $s->getTransferTime();
                    if ($t !== null) {
                        $latencies[] = $t * 1000.0; // ms
                    }
                },
            ],
            'fulfilled' => function ($response) use (&$errors) {
                if ($response->getStatusCode() >= 400) {
                    $errors++;
                }
            },
            'rejected' => function () use (&$errors) {
                $errors++;
            },
        ]))->promise()->wait();
        $wall = microtime(true) - $start;

        sort($latencies);

        return [
            'p50' => $this->pct($latencies, 50),
            'p95' => $this->pct($latencies, 95),
            'p99' => $this->pct($latencies, 99),
            'rps' => $wall > 0 ? round($total / $wall, 1) : 0,
            'err' => round(100 * $errors / max(1, $total), 2),
        ];
    }

    private function pct(array $sorted, int $p): float
    {
        if (empty($sorted)) {
            return 0.0;
        }
        $i = (int) ceil($p / 100 * count($sorted)) - 1;

        return round($sorted[max(0, min($i, count($sorted) - 1))], 1);
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
