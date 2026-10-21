# Reproducing the Paper's Results

This guide accompanies the paper *KnowVerse: An Academic Discussion Platform Based on an Auditable
Reputation Ledger and Role-Based Access Control*. It explains how to reproduce the results reported in
Tables 5–13. Paths are relative to the repository root; the raw output of every experiment reported in
the paper is archived in `benchmarks/results/`.

## 1. Requirements

PHP 8.2 or 8.3 with the `pdo_sqlite` and `pdo_mysql` extensions, Composer 2, Node.js 18 or later,
and MariaDB 10.4 or later (or MySQL 8) for the performance and concurrency experiments.

```bash
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
```

## 2. Functional tests — Tables 5, 6, 7 and 8

```bash
php artisan test
```

The suite (126 tests, 295 assertions) runs against an in-memory SQLite database and needs no other setup.

| Table | What it reports | Test files (`tests/Feature/`) |
|---|---|---|
| 5 | Tested security controls | `Security/AccessControlTest.php`, `Security/InjectionTest.php`, `Security/ContentGovernanceTest.php` |
| 6 | Anti-farming validation | `AntiFarmingTest.php`, `ReputationReversalIntegrityTest.php` |
| 7 | Privileges and RBAC decoupling | `ReputationPrivilegesTest.php`, `RoleManagementTest.php` |
| 8 | Ledger integrity under stress | `ReputationConsistencyTest.php`, `ReputationLedgerTest.php` |
| Sections 3.3, 6.4 | Account erasure behavior | `AccountErasureTest.php` |
| Section 6.5 | Atomic vote processing | `VoteAtomicityTest.php` |

## 3. Benchmark dataset and configuration — Tables 9 to 13

Create a separate MySQL/MariaDB database for the experiments (they write synthetic data), set it in
`.env`, then:

```bash
php artisan migrate --force
php artisan db:seed --class=RoleSeeder --force
php artisan knowverse:seed-benchmark --users=1000 --posts=10000 --comments=20000 --votes=100000 --tags=50
```

The performance measurements used a production configuration: PHP OPcache enabled for the web server,
`APP_ENV=production`, `APP_DEBUG=false`, and `php artisan config:cache`, `route:cache` and `view:cache`.
`benchmarks/README.md` describes every command and option in detail.

## 4. Experiments

| Table | Command | Raw results |
|---|---|---|
| 9 — HTTP latency, throughput, errors | `php artisan knowverse:benchmark-http --base=<server URL> --requests=400 --concurrency=10,50,100,200 --endpoints=home,posts,show,search --runs=5 --warmup=5` | `benchmarks/results/e2/` |
| 10 — Vote processing | `php artisan knowverse:benchmark-votes --samples=1000 --runs=5` | `benchmarks/results/e3/benchmark-votes*.csv`, `benchmarks/results/e3/votes-console.txt` |
| 11 — Real-time delivery | `php artisan reverb:start`, then `node benchmarks/ws-latency.mjs`, then `php artisan knowverse:benchmark-ws --count=100 --interval=50 --runs=5` | `benchmarks/results/e3/ws-e2e-*.json`, `benchmarks/results/e3/ws-*-console.txt` |
| 12 — Concurrent writes (8 processes) | `php artisan knowverse:benchmark-concurrency --workers=8 --ops=250 --awards=20 --repeat=5 --posts=10 --voters=50 --runs=5` (add `--baseline` for the original vote code) | `benchmarks/results/e4/atomic-8.csv`, `benchmarks/results/e4/baseline-8.csv` |
| 12 — Concurrent writes (16 processes) | `php artisan knowverse:benchmark-concurrency --workers=16 --ops=150 --awards=20 --repeat=5 --posts=5 --voters=20 --runs=5` (add `--baseline` for the original vote code) | `benchmarks/results/e4/atomic-16.csv`, `benchmarks/results/e4/baseline-16.csv` |
| 13 — Ledger versus counter-only | `php artisan knowverse:benchmark-ablation --samples=1000 --runs=5 --corrupt=50 --warmup=100 --backfill` | `benchmarks/results/e5/` |
| Section 6.5 — Which guard prevents a double reversal | `php artisan knowverse:benchmark-reversal-guards --workers=8,16 --awards=20 --repeat=5 --runs=5` | `benchmarks/results/e6/` |

Additional raw data:

| Folder | Content |
|---|---|
| `benchmarks/results/e1/` | One-factor-at-a-time diagnosis of the original latency (Section 7): development baseline, caches, production mode, OPcache, file sessions |
| `benchmarks/results/e4-before-ledger-lock/` | An early concurrency run, made before ledger writes were serialized per user |
| `benchmarks/results/*.csv` | Output of the most recent run of each command |

Each `environment.txt` and console log records the configuration that was active when the data was produced.
All experiments ran on a single workstation (Intel Core i7-10510U, 16 GB RAM, Windows 11, Apache 2.4 with
mod_php 8.2, MariaDB 10.4), so absolute numbers depend on the host; the commands report means and standard
deviations across runs, and the CSV files also contain 95% confidence intervals.
