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

---

## 1. HTTP response time, throughput and concurrency

### Option A — Artisan (zero install)

```bash
php artisan knowverse:benchmark-http
# or customise:
php artisan knowverse:benchmark-http \
    --requests=1000 --concurrency=10,50,100,200 --endpoints=home,posts,show,search
```

Prints a per-endpoint × per-concurrency table (p50 / p95 / p99 ms, req/s, error %)
and writes `storage/benchmarks/benchmark-http.csv`.

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
php artisan knowverse:benchmark-votes --samples=1000
```

Times the full server-side cascade — **vote → reputation ledger → activity log →
notification** — and reports mean / p50 / p95 / p99 latency plus single-process
throughput. It then runs the ledger consistency oracle and reports the number of
**invariant violations (expected: 0)**. Writes `storage/benchmarks/benchmark-votes.csv`.

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

## Summary

| Command / tool | Output | Measures |
|---|---|---|
| `knowverse:seed-benchmark` | — | Synthetic dataset generation |
| `knowverse:benchmark-http` / k6 | `benchmark-http.csv` | HTTP latency, throughput, error rate |
| `knowverse:benchmark-votes` | `benchmark-votes.csv` | Vote cascade latency + ledger integrity |
| `knowverse:benchmark-ws` + `ws-latency.mjs` | console summary | Publish and end-to-end delivery latency |

Re-seed with `knowverse:seed-benchmark --fresh` between runs so that results stay
comparable.
