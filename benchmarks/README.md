# Performance Benchmark Harness

Reproducible measurements for the three performance-critical paths in KnowVerse:
HTTP read throughput, the server-side vote → reputation-ledger cascade, and
real-time WebSocket delivery via Laravel Reverb.

Run these against a **real MySQL database** with a synthetic dataset — never the
SQLite test database. All CSV output is written to `storage/benchmarks/`, which is
excluded from version control.

---

## 0. One-time setup

```bash
# 1. Seed a synthetic dataset (wipes prior synthetic content)
php artisan knowverse:seed-benchmark --fresh \
    --users=1000 --posts=10000 --comments=20000 --votes=100000 --tags=50

# 2. Start the stack (each in its own terminal)
php artisan serve            # HTTP on :8000
php artisan reverb:start     # WebSockets on :8080  (needed for §3 only)
php artisan queue:work       # background notifications
```

Record the host specification (CPU / RAM / OS / PHP / MySQL versions) alongside any
results you report — the absolute numbers are only meaningful with it.

### Measure a production configuration

Development defaults dominate latency, so configure the server as for production
before measuring, and state the configuration next to the results:

```bash
# php.ini: zend_extension=opcache and opcache.enable=1 for the web server's PHP
# .env:    APP_ENV=production, APP_DEBUG=false
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Without a cached configuration, a multithreaded web server (for example Apache
`mpm_winnt` with thread-safe PHP) can load `.env` concurrently and fail a fraction of
requests. Run `php artisan config:clear` afterwards: while the configuration is cached,
`php artisan test` does not read `phpunit.xml`.

### Repeated runs

The HTTP, vote, WebSocket and concurrency commands accept `--runs=N`. Each run repeats
the complete measurement; results are reported as **mean ± sample standard deviation**,
and the CSV files add the 95% confidence interval (Student's t). The HTTP command sweeps
the whole endpoint × concurrency grid once per run, so slow changes in machine state are
spread across all cells. Per-run values are written to a separate `*-runs.csv` file.

---

## 1. HTTP response time, throughput and concurrency

### Option A — Artisan (zero install)

```bash
php artisan knowverse:benchmark-http
# or customise:
php artisan knowverse:benchmark-http --base=http://127.0.0.1:8001 \
    --requests=400 --concurrency=10,50,100,200 --endpoints=home,posts,show,search \
    --runs=5 --warmup=5
```

Prints a per-endpoint × per-concurrency table (p50 / p95 / p99 ms, req/s, error %)
and writes `storage/benchmarks/benchmark-http.csv` (summary) and
`benchmark-http-runs.csv` (one row per run). Use at least as many requests per cell as
the highest concurrency level, otherwise that level is never reached.

### Option B — k6 (recommended where available)

```bash
# install: https://k6.io/docs/get-started/installation/
k6 run -e BASE=http://127.0.0.1:8000 -e POST_ID=1 benchmarks/k6/load.js
```

Ramps virtual users 10 → 50 → 100 → 200; the end-of-run summary reports
`http_req_duration` percentiles (per endpoint via the `name` tag), throughput
(`http_reqs`) and the `http_req_failed` rate.

> `php artisan serve` handles limited concurrency — set `PHP_CLI_SERVER_WORKERS=4`
> or higher in `.env`. For realistic high-concurrency figures, serve the application
> through Nginx/Apache + PHP-FPM and point `--base` / `BASE` at that instead. Always
> state the server configuration next to the results.

---

## 2. Vote-processing latency and ledger integrity

```bash
php artisan knowverse:benchmark-votes --samples=1000 --runs=5
```

Times the full server-side cascade — **vote → reputation ledger → activity log →
notification** — and reports mean / p50 / p95 / p99 latency plus single-process
throughput. It then runs the ledger consistency oracle and reports the number of
**invariant violations (expected: 0)** after every run. Writes
`storage/benchmarks/benchmark-votes.csv` and `benchmark-votes-runs.csv`.

Broadcasting is forced to the `null` driver for this run, so the figure isolates the
database cascade from network delivery.

---

## 3. WebSocket (Reverb) delivery efficiency

**Server-side publish latency (zero install):**

```bash
php artisan knowverse:benchmark-ws --count=100 --interval=50
```

Reports application → Reverb publish latency (p50 / p95 / p99).

**End-to-end delivery latency (event → client):**

```bash
npm install ws            # one-time; not a project dependency

# terminal 1:
VITE_REVERB_APP_KEY=<your REVERB_APP_KEY> node benchmarks/ws-latency.mjs
# terminal 2:
php artisan knowverse:benchmark-ws --count=100 --interval=50
# then Ctrl+C the node process to print the latency summary
```

The Node probe subscribes to the public `benchmark` channel and computes
`client_now − server_timestamp` for each ping. Run both processes on the **same
host** so that the wall clock is shared, otherwise clock skew dominates the result.

To probe *maximum concurrent connections*, open many probe clients and observe
Reverb's console output and resource usage against its `max_connections` setting.

---

## 4. Consistency under concurrent writes (MySQL/MariaDB only)

```bash
php artisan knowverse:benchmark-concurrency --workers=8 --runs=5
php artisan knowverse:benchmark-concurrency --workers=8 --runs=5 --baseline
```

Starts parallel PHP processes that begin at the same instant. Phase A has every
process reverse the same user's awards for the same discussion; exactly one reversal
per award must result. Phase B has every process cast, flip and retract votes on a
shared set of discussions. After each run the command checks the ledger invariant, for
entries reversed more than once or over-reversed, vote counters against the votes table,
and each author's vote reputation against the votes that remain. It exits non-zero on
any violation. `--baseline` runs phase B through the earlier, non-atomic vote write path
for comparison. Add `-v` to print the first message of each worker error type. Writes
`storage/benchmarks/benchmark-concurrency.csv` (or `-baseline.csv`).

The command writes synthetic discussions and votes: run it against a benchmark database.

---

## 5. Ledger versus a plain counter (ablation)

```bash
php artisan knowverse:benchmark-ablation --samples=1000 --runs=5 --corrupt=50 --backfill
```

Part A times the same vote workload with reputation recorded in the ledger and with
reputation kept only as a counter column, alternating the modes run by run. Part B
corrupts the counters of randomly chosen users, then checks that a scan against the
ledger finds exactly those users and that recalculation restores their exact values.
`--backfill` first derives the ledger entries implied by the seeded posts, comments and
votes (the bulk seeder writes none), so both parts run against a ledger of realistic
size. Writes `benchmark-ablation-cost.csv`, `benchmark-ablation-cost-runs.csv` and
`benchmark-ablation-recovery.csv`. Run it against a benchmark database only: it rewrites
every user's reputation counter.

---

## Summary

| Command / tool | Output | Measures |
|---|---|---|
| `knowverse:seed-benchmark` | — | Synthetic dataset generation |
| `knowverse:benchmark-http` / k6 | `benchmark-http.csv`, `benchmark-http-runs.csv` | HTTP latency, throughput, error rate |
| `knowverse:benchmark-votes` | `benchmark-votes.csv`, `benchmark-votes-runs.csv` | Vote cascade latency + ledger integrity |
| `knowverse:benchmark-ws` + `ws-latency.mjs` | console summary | Publish and end-to-end delivery latency |
| `knowverse:benchmark-concurrency` | `benchmark-concurrency.csv` | Ledger and vote consistency under parallel writes |
| `knowverse:benchmark-ablation` | `benchmark-ablation-*.csv` | Ledger cost versus a counter; corruption detection and recovery |

Re-seed with `knowverse:seed-benchmark --fresh` between runs so that results stay
comparable.
