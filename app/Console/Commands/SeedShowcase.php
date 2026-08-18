<?php

namespace App\Console\Commands;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Models\Vote;
use App\Services\ReputationService;
use Illuminate\Console\Command;

/**
 * Seeds a few rich, realistic academic discussions (Markdown + LaTeX + code blocks),
 * with scholars whose reputation is built entirely through the append-only ledger
 * (so the Reputation Dashboard is fully auditable: total == sum of transactions).
 *
 *   php artisan knowverse:seed-showcase
 *   php artisan knowverse:seed-showcase --fresh    # regenerate from scratch
 */
class SeedShowcase extends Command
{
    protected $signature = 'knowverse:seed-showcase {--fresh : Remove previously-seeded showcase content first}';

    protected $description = 'Seed rich, realistic academic discussions (Markdown/LaTeX/code) + ledger-backed scholars for demo screenshots.';

    public function handle(): int
    {
        config(['broadcasting.default' => 'null']); // no live WebSocket calls while seeding
        $this->callSilent('db:seed', ['--class' => \Database\Seeders\RoleSeeder::class, '--force' => true]);

        // ---- Post bodies (NOWDOC: no PHP interpolation, so the $ LaTeX is preserved) ----

        $body1 = <<<'BODY'
Divide-and-conquer is one of the most influential paradigms in algorithm design. The strategy is deceptively simple: split a problem into smaller subproblems, solve each recursively, and combine the results.

## The Recurrence

If a problem of size $n$ is divided into $a$ subproblems of size $n/b$, with a combination cost of $f(n)$, the running time obeys the recurrence:

$$ T(n) = a\,T\!\left(\frac{n}{b}\right) + f(n) $$

The **Master Theorem** solves this in closed form by comparing $f(n)$ against $n^{\log_b a}$.

## When Does It Help?

Divide-and-conquer shines when:

- subproblems are **independent** (no shared state),
- the combine step is cheaper than re-solving from scratch,
- the recursion tree is **balanced**.

Classic examples include merge sort, which achieves $O(n \log n)$ comparisons, and Strassen's matrix multiplication.

## A Reference Implementation

```python
def merge_sort(a):
    if len(a) <= 1:
        return a
    mid = len(a) // 2
    left = merge_sort(a[:mid])
    right = merge_sort(a[mid:])
    return merge(left, right)
```

The elegance of the paradigm lies in turning an intractable problem into a recurrence we can reason about formally — and the Master Theorem turns that recurrence into an answer.
BODY;

        $body2 = <<<'BODY'
Audit logs are only trustworthy if they cannot be silently altered. A **tamper-evident log** uses cryptographic hashing so that any modification to a past entry becomes detectable.

## The Hash Chain

Each entry stores the hash of the previous entry alongside its own data. If an entry holds data $d$ and the previous hash is $p$, its hash is:

$$ h = H(p \mathbin{\Vert} d) $$

where $H$ is a collision-resistant hash function and $\Vert$ denotes concatenation. Altering any earlier entry changes every subsequent hash — so tampering cannot hide.

## Properties

A well-designed append-only log provides:

- **Integrity** — past entries cannot be modified undetected,
- **Ordering** — the chain fixes a total order of events,
- **Auditability** — any party can recompute and verify the chain.

## Verifying the Chain

```python
def verify(entries):
    prev = GENESIS
    for e in entries:
        if e.hash != H(prev + e.data):
            return False
        prev = e.hash
    return True
```

This is precisely the principle behind an immutable reputation ledger: every change is a verifiable transaction rather than an overwrite.
BODY;

        $body3 = <<<'BODY'
Few results capture the surprise of mathematical analysis like the Basel problem — the question of what value is approached by the sum of the reciprocals of the squares.

## The Statement

We seek the exact value of the infinite series:

$$ \sum_{n=1}^{\infty} \frac{1}{n^2} = \frac{\pi^2}{6} $$

That a sum of purely rational terms converges to a value involving $\pi$ is, at first sight, astonishing.

## Why It Converges

The series converges because its terms shrink quickly enough:

- each term is bounded above by $\frac{1}{n(n-1)}$ for $n \geq 2$,
- the partial sums form a bounded, increasing sequence,
- by the monotone convergence theorem, a limit must exist.

## A Numerical Sanity Check

```python
total = sum(1 / n**2 for n in range(1, 1_000_000))
print(total)            # ~1.6449
print(math.pi**2 / 6)   # 1.6449...
```

Euler's solution connected discrete summation to the continuous world of $\pi$, foreshadowing deep links between number theory and analysis.
BODY;

        // ---- Scholar definitions (target = final reputation, built via the ledger) ----
        $scholarData = [
            ['full_name' => 'Dr. Elena Vasquez', 'username' => 'elena_vasquez', 'email' => 'elena.vasquez@showcase.test', 'academic_title' => 'Dr.',  'target' => 510],
            ['full_name' => 'Prof. Kenji Tanaka', 'username' => 'kenji_tanaka', 'email' => 'kenji.tanaka@showcase.test', 'academic_title' => 'Prof.', 'target' => 320],
            ['full_name' => 'Dr. Amara Okafor',  'username' => 'amara_okafor', 'email' => 'amara.okafor@showcase.test', 'academic_title' => 'Dr.',  'target' => 145],
            ['full_name' => "Liam O'Brien",       'username' => 'liam_obrien',  'email' => 'liam.obrien@showcase.test',  'academic_title' => null,   'target' => 60],
        ];

        $posts = [
            [
                'author' => 0, 'pinned' => true,
                'title' => 'Divide and Conquer: A Tour of the Master Theorem',
                'body' => $body1, 'tags' => ['Algorithms', 'Computer Science'],
                'comments' => [
                    'An excellent summary. It is worth adding that the regularity condition is what trips up most students applying case 3.',
                    'The merge sort example makes the balanced-tree intuition very clear. Thank you for the writeup.',
                ],
            ],
            [
                'author' => 1, 'pinned' => false,
                'title' => 'Tamper-Evident Logs: Why Hash Chains Matter',
                'body' => $body2, 'tags' => ['Cybersecurity', 'Cryptography'],
                'comments' => [
                    'The connection to append-only reputation systems is spot on — integrity without trusting a central authority.',
                    'Could you expand on how Merkle trees improve verification cost over a linear chain?',
                ],
            ],
            [
                'author' => 2, 'pinned' => false,
                'title' => 'The Basel Problem and the Beauty of Convergent Series',
                'body' => $body3, 'tags' => ['Mathematics'],
                'comments' => [
                    "Euler's original argument is brilliant, even if it predates a rigorous notion of convergence.",
                    'The numerical check is a nice touch for building intuition before the proof.',
                ],
            ],
        ];

        // ---- --fresh: remove prior showcase posts AND scholars (so we rebuild cleanly) ----
        if ($this->option('fresh')) {
            $titles = array_column($posts, 'title');
            foreach (Post::withoutGlobalScopes()->whereIn('title', $titles)->get() as $op) {
                Vote::where('target_type', 'post')->where('target_id', $op->id)->delete();
                $op->forceDelete(); // cascades comments
            }
            User::whereIn('email', array_column($scholarData, 'email'))->forceDelete(); // cascades reputations/votes
            $this->warn('Removed previous showcase content.');
        }

        // ---- Scholars (start at zero; reputation is earned through the ledger) ----
        $scholars = collect($scholarData)->map(fn ($d) => User::firstOrCreate(
            ['email' => $d['email']],
            [
                'full_name' => $d['full_name'],
                'username' => $d['username'],
                'academic_title' => $d['academic_title'],
                'password' => 'password',
                'role_id' => 1,
                'reputation_points' => 0,
                'email_verified_at' => now(),
                'public_follow_lists' => true,
            ]
        ))->values();

        // ---- Tags ----
        $tagIds = collect(['Algorithms', 'Computer Science', 'Cybersecurity', 'Cryptography', 'Mathematics'])
            ->mapWithKeys(fn ($n) => [$n => Tag::firstOrCreate(['name' => $n])->id]);

        // ---- Posts (idempotent: skip if already present) ----
        $urls = [];
        foreach ($posts as $p) {
            if (Post::withoutGlobalScopes()->where('title', $p['title'])->exists()) {
                $urls[] = route('posts.show', Post::withoutGlobalScopes()->where('title', $p['title'])->first());
                $this->line('  (exists) '.$p['title']);

                continue;
            }

            $author = $scholars[$p['author']];
            $post = Post::create([
                'user_id' => $author->id,
                'title' => $p['title'],
                'body' => $p['body'],
                'status' => Post::STATUS_PUBLISHED,
            ]);
            $post->tags()->sync(collect($p['tags'])->map(fn ($t) => $tagIds[$t])->all());

            if ($p['pinned']) {
                $post->update(['pinned_at' => now()]);
            }

            foreach ($p['comments'] as $i => $body) {
                Comment::create([
                    'post_id' => $post->id,
                    'user_id' => $scholars[($p['author'] + $i + 1) % $scholars->count()]->id,
                    'body' => $body,
                ]);
            }

            foreach ($scholars as $voter) {
                if ($voter->id !== $author->id) {
                    Vote::castVote($voter, $post, 1);
                }
            }

            $urls[] = route('posts.show', $post);
            $this->info('  created: '.$p['title']);
        }

        // ---- Build a consistent, ledger-backed reputation history per scholar ----
        $this->info('Building auditable reputation ledgers...');
        $reputation = app(ReputationService::class);
        $allPosts = Post::withoutGlobalScopes()->whereIn('title', array_column($posts, 'title'))->get();
        // Weighted action pool (mostly received upvotes, some comments, occasional pick / downvote)
        $pool = ['post_upvoted', 'post_upvoted', 'post_upvoted', 'post_upvoted',
            'comment_upvoted', 'comment_upvoted', 'comment_created',
            'authors_pick_received', 'post_downvoted'];

        foreach ($scholars as $idx => $scholar) {
            $scholar->refresh(); // include reputation already earned from posts/comments/votes
            $target = $scholarData[$idx]['target'];
            $guard = 0;
            while ($scholar->reputation_points < $target && $guard < 3000) {
                $guard++;
                $action = $pool[array_rand($pool)];
                $source = (in_array($action, ['post_upvoted', 'post_downvoted', 'authors_pick_received']) && $allPosts->isNotEmpty())
                    ? $allPosts->random()
                    : null;
                $reputation->award($scholar, $action, null, $source);
            }
        }

        $this->newLine();
        $this->info('Showcase discussions ready. Open these for your screenshots:');
        foreach ($urls as $u) {
            $this->line('  '.$u);
        }
        $this->line('Top Scholars: '.$scholars->map(fn ($s) => $s->display_name.' ('.$s->reputation_points.')')->implode(', '));
        $this->warn('Showcase scholars use the password "password".');

        return self::SUCCESS;
    }
}
