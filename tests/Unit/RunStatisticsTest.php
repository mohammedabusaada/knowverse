<?php

use App\Console\Commands\Concerns\SummarisesRuns;

/*
 * Pure unit coverage for the statistics used to report repeated benchmark runs.
 * Reference values: [2, 4, 4, 4, 5, 5, 7, 9] has mean 5 and sample SD sqrt(32 / 7).
 */

beforeEach(function () {
    $this->stats = new class
    {
        use SummarisesRuns;

        public function __call($method, $args)
        {
            return $this->{$method}(...$args);
        }
    };
});

it('computes the mean and the sample standard deviation', function () {
    $values = [2, 4, 4, 4, 5, 5, 7, 9];

    expect($this->stats->mean($values))->toBe(5.0)
        ->and($this->stats->sampleSd($values))->toEqualWithDelta(sqrt(32 / 7), 1e-9);
});

it('computes a 95% confidence interval with Student\'s t for small samples', function () {
    $values = [2, 4, 4, 4, 5, 5, 7, 9]; // 7 degrees of freedom → t = 2.365

    expect($this->stats->ci95($values))->toEqualWithDelta(2.365 * sqrt(32 / 7) / sqrt(8), 1e-9);
});

it('reports no spread for a single run', function () {
    expect($this->stats->sampleSd([42.0]))->toBe(0.0)
        ->and($this->stats->ci95([42.0]))->toBe(0.0)
        ->and($this->stats->formatSummary($this->stats->summarise([42.0])))->toBe('42.0');
});

it('formats repeated runs as mean ± SD', function () {
    $summary = $this->stats->summarise([10.0, 12.0, 14.0]); // mean 12, SD 2

    expect($this->stats->formatSummary($summary))->toBe('12.0 ± 2.0')
        ->and($this->stats->csvSummary($summary))->toBe('12,2,'.round(4.303 * 2 / sqrt(3), 2));
});
