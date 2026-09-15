<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\User;
use App\Models\Vote;
use App\Services\ReputationService;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;

/**
 * Worker process for knowverse:benchmark-concurrency. Loads its data, waits for the
 * shared start barrier, performs its operations, and prints a JSON report.
 */
class ConcurrencyWorker extends Command
{
    protected $signature = 'knowverse:concurrency-worker
        {--worker=0 : Worker index}
        {--start-at=0 : Unix time (seconds) at which all workers begin}
        {--mode=votes : reverse | votes}
        {--user= : Phase A: user whose awards are reversed}
        {--post= : Phase A: discussion the awards refer to}
        {--repeat=1 : Phase A: reversal attempts}
        {--ops=100 : Phase B: vote operations}
        {--voters= : Phase B: comma-separated voter ids}
        {--posts= : Phase B: comma-separated discussion ids}
        {--baseline : Phase B: use the non-atomic vote write path}';

    protected $description = 'Internal worker for knowverse:benchmark-concurrency.';

    protected $hidden = true;

    public function handle(): int
    {
        config(['broadcasting.default' => 'null']);
        mt_srand((int) $this->option('worker') * 7919 + getmypid());

        $ops = 0;
        $errors = [];
        $examples = [];
        $record = function (\Throwable $e) use (&$errors, &$examples) {
            $key = $e instanceof QueryException
                ? 'SQLSTATE '.($e->errorInfo[0] ?? $e->getCode()).'/'.($e->errorInfo[1] ?? '?')
                : class_basename($e);
            $errors[$key] = ($errors[$key] ?? 0) + 1;
            $examples[$key] ??= substr($e->getMessage(), 0, 400);
        };

        if ($this->option('mode') === 'reverse') {
            $user = User::findOrFail((int) $this->option('user'));
            $post = Post::withoutGlobalScopes()->findOrFail((int) $this->option('post'));
            $service = app(ReputationService::class);
            $this->waitForBarrier();

            for ($i = 0; $i < max(1, (int) $this->option('repeat')); $i++) {
                try {
                    $service->remove($user, 'post_upvoted', $post);
                    $ops++;
                } catch (\Throwable $e) {
                    $record($e);
                }
            }
        } else {
            $voters = User::whereIn('id', explode(',', (string) $this->option('voters')))->get()->all();
            $posts = Post::withoutGlobalScopes()->whereIn('id', explode(',', (string) $this->option('posts')))->get()->all();
            $this->waitForBarrier();

            for ($i = 0; $i < max(1, (int) $this->option('ops')); $i++) {
                $voter = $voters[array_rand($voters)];
                $post = $posts[array_rand($posts)];
                $roll = mt_rand(1, 100);

                try {
                    if ($this->option('baseline')) {
                        $this->baselineVote($voter, $post, $roll <= 50 ? 1 : ($roll <= 80 ? -1 : 0));
                    } elseif ($roll <= 50) {
                        Vote::castVote($voter, $post, 1);
                    } elseif ($roll <= 80) {
                        Vote::castVote($voter, $post, -1);
                    } else {
                        Vote::retract($voter, $post);
                    }
                    $ops++;
                } catch (\Throwable $e) {
                    $record($e);
                }
            }
        }

        $this->line(json_encode(['worker' => (int) $this->option('worker'), 'ops' => $ops, 'errors' => $errors, 'examples' => $examples]));

        return self::SUCCESS;
    }

    /**
     * The vote write path as it was before votes became atomic: the vote row is written
     * on its own and the observer's side effects run in separate transactions.
     */
    private function baselineVote(User $voter, Post $post, int $value): void
    {
        $keys = ['user_id' => $voter->id, 'target_id' => $post->id, 'target_type' => $post->getMorphClass()];

        if ($value === 0) {
            Vote::where($keys)->first()?->delete();
        } else {
            Vote::updateOrCreate($keys, ['value' => $value]);
        }
    }

    private function waitForBarrier(): void
    {
        $startAt = (float) $this->option('start-at');
        while (microtime(true) < $startAt) {
            usleep(1000);
        }
    }
}
