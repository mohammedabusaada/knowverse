<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\Reputation;
use App\Models\User;
use App\Services\ReputationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Which guard prevents a double reversal? (MySQL/MariaDB only)
 *
 * Repeats the reversal race of knowverse:benchmark-concurrency with each combination of the
 * two guards switched on or off: the row locks of the ledger service (the per-user lock and
 * the lock on the selected entry) and the unique constraint on reputations.reverses_id.
 * Each run uses a user and a discussion created for it, and permanently deletes them
 * afterwards, so that the unique constraint can be restored once the experiment ends.
 */
class BenchmarkReversalGuards extends BenchmarkConcurrency
{
    protected $signature = 'knowverse:benchmark-reversal-guards
        {--workers=8,16 : Comma-separated numbers of parallel PHP processes}
        {--awards=20 : Awards created before each race}
        {--repeat=5 : Reversal attempts per worker}
        {--runs=5 : Runs per configuration}';

    protected $description = 'Reversal race with the row locks and the unique constraint switched on and off (MySQL/MariaDB).';

    private const CONFIGURATIONS = [
        'locks + unique' => [true, true],
        'locks only' => [true, false],
        'unique only' => [false, true],
        'neither' => [false, false],
    ];

    public function handle(): int
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            $this->error('This experiment needs MySQL or MariaDB: SQLite serialises all writes.');

            return self::FAILURE;
        }

        $workerCounts = array_map('intval', explode(',', (string) $this->option('workers')));
        $awards = max(1, (int) $this->option('awards'));
        $repeat = max(1, (int) $this->option('repeat'));
        $runs = max(1, (int) $this->option('runs'));

        $this->info("Reversal guards on '".DB::connection()->getDatabaseName()."': {$awards} awards, {$repeat} attempts per worker, {$runs} run(s) per configuration");

        $rows = [];
        $csv = "configuration,locks,unique_constraint,workers,run,awards,attempts,reversals,entries_reversed_twice,extra_reversals,invariant_ok,worker_errors\n";
        $guardedFailures = 0;

        try {
            foreach (self::CONFIGURATIONS as $name => [$locks, $unique]) {
                $this->setUniqueConstraint($unique);

                foreach ($workerCounts as $workers) {
                    $this->line("  {$name}, {$workers} workers ...");
                    $total = ['reversals' => 0, 'twice' => 0, 'extra' => 0, 'invariant' => 0, 'errors' => []];

                    for ($run = 1; $run <= $runs; $run++) {
                        $r = $this->race($workers, $awards, $repeat, $locks);
                        $errorText = empty($r['errors']) ? '0' : implode('; ', array_map(fn ($k, $v) => "{$k}×{$v}", array_keys($r['errors']), $r['errors']));

                        $total['reversals'] += $r['reversals'];
                        $total['twice'] += $r['twice'];
                        $total['extra'] += max(0, $r['reversals'] - $awards);
                        $total['invariant'] += $r['invariant_ok'] ? 0 : 1;
                        foreach ($r['errors'] as $k => $n) {
                            $total['errors'][$k] = ($total['errors'][$k] ?? 0) + $n;
                        }

                        $csv .= implode(',', [
                            '"'.$name.'"', (int) $locks, (int) $unique, $workers, $run, $awards, $workers * $repeat,
                            $r['reversals'], $r['twice'], max(0, $r['reversals'] - $awards), $r['invariant_ok'] ? 1 : 0,
                            '"'.str_replace('"', "'", $errorText).'"',
                        ])."\n";
                    }

                    if ($locks && $unique && ($total['twice'] || $total['extra'] || $total['invariant'])) {
                        $guardedFailures++;
                    }

                    $rows[] = [
                        $name, $workers,
                        ($workers * $repeat * $runs).' → '.$total['reversals'].' (of '.($awards * $runs).')',
                        $total['twice'], $total['extra'], $total['invariant'],
                        empty($total['errors']) ? '0' : implode('; ', array_map(fn ($k, $v) => "{$k}×{$v}", array_keys($total['errors']), $total['errors'])),
                    ];
                }
            }
        } finally {
            $this->setUniqueConstraint(true);
        }

        $this->newLine();
        $this->table(
            ['Configuration', 'Workers', 'Attempts → reversals', 'Entries reversed twice', 'Extra reversals', 'Invariant violations', 'Worker errors'],
            $rows
        );
        $this->writeCsv('benchmark-reversal-guards.csv', $csv);

        if ($guardedFailures > 0) {
            $this->error('The complete system (locks + unique) produced a double reversal.');

            return self::FAILURE;
        }
        $this->info('The complete system produced no double reversal; the unique constraint is restored.');

        return self::SUCCESS;
    }

    private function race(int $workers, int $awards, int $repeat, bool $locks): array
    {
        config(['broadcasting.default' => 'null']);

        $now = now();
        $userId = DB::table('users')->insertGetId([
            'username' => 'race_'.Str::random(10),
            'email' => 'race_'.Str::random(10).'@bench.local',
            'full_name' => 'Reversal Race',
            'password' => 'unused',
            'role_id' => 1,
            'reputation_points' => 0,
            'email_verified_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $postId = DB::table('posts')->insertGetId([
            'user_id' => $userId,
            'title' => 'Reversal race',
            'body' => 'Synthetic discussion created by the reversal-guards experiment.',
            'status' => 'published',
            'is_hidden' => 0,
            'view_count' => 0,
            'upvote_count' => 0,
            'downvote_count' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $user = User::find($userId);
        $post = Post::withoutGlobalScopes()->find($postId);
        $service = app(ReputationService::class);
        for ($i = 0; $i < $awards; $i++) {
            $service->award($user, 'post_upvoted', null, $post);
        }

        $result = $this->launch($workers, ['--mode=reverse', "--user={$userId}", "--post={$postId}", "--repeat={$repeat}", '--locks='.(int) $locks]);

        $reversals = Reputation::where('user_id', $userId)->where('action', 'post_upvoted_reverted')->count();
        $twice = DB::query()->fromSub(
            DB::table('reputations')->where('user_id', $userId)->whereNotNull('reverses_id')
                ->groupBy('reverses_id')->havingRaw('COUNT(*) > 1')->select('reverses_id'),
            'd'
        )->count();
        $invariantOk = (int) DB::table('users')->where('id', $userId)->value('reputation_points')
            === (int) Reputation::where('user_id', $userId)->sum('delta');

        // Permanent deletion removes the user's ledger entries through the foreign-key cascade,
        // so no double reversal survives to block the restoration of the unique constraint.
        DB::table('posts')->where('id', $postId)->delete();
        DB::table('users')->where('id', $userId)->delete();

        return ['reversals' => $reversals, 'twice' => $twice, 'invariant_ok' => $invariantOk, 'errors' => $result['errors']];
    }

    /**
     * Switches the unique constraint on reputations.reverses_id on or off. While it is off, a
     * plain index takes its place, because the foreign key on the column requires an index.
     */
    private function setUniqueConstraint(bool $on): void
    {
        $indexes = collect(DB::select("SHOW INDEX FROM reputations WHERE Column_name = 'reverses_id'"));
        $unique = $indexes->firstWhere('Non_unique', 0);
        $plain = $indexes->firstWhere('Key_name', 'reputations_reverses_id_plain');

        if ($on && ! $unique) {
            DB::statement('ALTER TABLE reputations ADD UNIQUE INDEX reputations_reverses_id_unique (reverses_id)');
        }
        if ($on && $plain) {
            DB::statement('ALTER TABLE reputations DROP INDEX reputations_reverses_id_plain');
        }
        if (! $on && $unique) {
            if (! $plain) {
                DB::statement('ALTER TABLE reputations ADD INDEX reputations_reverses_id_plain (reverses_id)');
            }
            DB::statement("ALTER TABLE reputations DROP INDEX `{$unique->Key_name}`");
        }
    }
}
