<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\TransferStats;

/**
 * Concurrent HTTP load test over the public read endpoints. Produces response-time
 * percentiles, throughput, and error rate per (endpoint, concurrency) cell.
 *
 * Prerequisites: the app must be serving (e.g. `php artisan serve`) with a seeded DB.
 *   php artisan knowverse:seed-benchmark --fresh
 *   php artisan serve            # in another terminal
 *   php artisan knowverse:benchmark-http
 */
class BenchmarkHttp extends Command
{
    protected $signature = 'knowverse:benchmark-http
        {--base=http://127.0.0.1:8000 : Base URL of the running app}
        {--requests=500 : Requests per (endpoint, concurrency) cell}
        {--concurrency=10,50,100,200 : Comma-separated concurrency levels}
        {--endpoints=home,posts,show,search : Endpoints (home,posts,show,search)}';

    protected $description = 'Concurrent HTTP load test → response-time percentiles, throughput, error rate.';

    public function handle(): int
    {
        $base = rtrim($this->option('base'), '/');
        $perCell = max(1, (int) $this->option('requests'));
        $concLevels = array_filter(array_map('intval', explode(',', $this->option('concurrency'))));
        $endpointKeys = array_map('trim', explode(',', $this->option('endpoints')));

        $postId = DB::table('posts')->where('status', 'published')->where('is_hidden', false)->value('id')
            ?? DB::table('posts')->value('id');

        $paths = [
            'home'   => '/',
            'posts'  => '/posts',
            'show'   => $postId ? "/posts/{$postId}" : null,
            'search' => '/search?q=benchmark',
        ];

        // Connectivity check.
        try {
            (new Client(['timeout' => 5]))->get($base.'/', ['http_errors' => false]);
        } catch (\Throwable $e) {
            $this->error("Cannot reach {$base}. Start the app first:  php artisan serve");
            return self::FAILURE;
        }

        $this->info("Load testing {$base}  ({$perCell} req/cell)");
        $rows = [];
        foreach ($endpointKeys as $key) {
            $path = $paths[$key] ?? null;
            if ($path === null) {
                $this->warn("Skipping unknown/unavailable endpoint '{$key}'");
                continue;
            }
            foreach ($concLevels as $conc) {
                $this->line("  {$key} @ concurrency {$conc} ...");
                $r = $this->runCell($base.$path, $perCell, $conc);
                $rows[] = [$key, $conc, $r['p50'], $r['p95'], $r['p99'], $r['rps'], $r['err']];
            }
        }

        $this->newLine();
        $this->table(['Endpoint', 'Conc', 'p50 ms', 'p95 ms', 'p99 ms', 'req/s', 'err %'], $rows);

        $csv = "endpoint,concurrency,p50_ms,p95_ms,p99_ms,throughput_rps,error_pct\n";
        foreach ($rows as $r) {
            $csv .= implode(',', $r)."\n";
        }
        $this->writeCsv('benchmark-http.csv', $csv);

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
