<?php

namespace App\Console\Commands\Concerns;

/**
 * Descriptive statistics for repeated benchmark runs: mean, sample standard
 * deviation and a 95% confidence interval for the mean (Student's t).
 */
trait SummarisesRuns
{
    /**
     * Two-sided 97.5% quantiles of Student's t distribution, by degrees of freedom.
     */
    private static array $tQuantiles = [
        1 => 12.706, 2 => 4.303, 3 => 3.182, 4 => 2.776, 5 => 2.571, 6 => 2.447,
        7 => 2.365, 8 => 2.306, 9 => 2.262, 10 => 2.228, 11 => 2.201, 12 => 2.179,
        13 => 2.160, 14 => 2.145, 15 => 2.131, 16 => 2.120, 17 => 2.110, 18 => 2.101,
        19 => 2.093, 20 => 2.086, 21 => 2.080, 22 => 2.074, 23 => 2.069, 24 => 2.064,
        25 => 2.060, 26 => 2.056, 27 => 2.052, 28 => 2.048, 29 => 2.045, 30 => 2.042,
    ];

    protected function mean(array $values): float
    {
        return empty($values) ? 0.0 : array_sum($values) / count($values);
    }

    /**
     * Sample standard deviation (n − 1). Zero when fewer than two values exist.
     */
    protected function sampleSd(array $values): float
    {
        $n = count($values);
        if ($n < 2) {
            return 0.0;
        }

        $mean = $this->mean($values);
        $squares = array_sum(array_map(fn ($v) => ($v - $mean) ** 2, $values));

        return sqrt($squares / ($n - 1));
    }

    /**
     * Half-width of the 95% confidence interval for the mean. Zero for a single run.
     */
    protected function ci95(array $values): float
    {
        $n = count($values);
        if ($n < 2) {
            return 0.0;
        }

        $t = self::$tQuantiles[$n - 1] ?? 1.96;

        return $t * $this->sampleSd($values) / sqrt($n);
    }

    /**
     * @return array{n: int, mean: float, sd: float, ci95: float}
     */
    protected function summarise(array $values): array
    {
        return [
            'n' => count($values),
            'mean' => $this->mean($values),
            'sd' => $this->sampleSd($values),
            'ci95' => $this->ci95($values),
        ];
    }

    /**
     * "mean ± SD" for repeated runs, or the plain value for a single run.
     */
    protected function formatSummary(array $summary, int $decimals = 1): string
    {
        $mean = number_format($summary['mean'], $decimals, '.', '');

        return $summary['n'] > 1
            ? $mean.' ± '.number_format($summary['sd'], $decimals, '.', '')
            : $mean;
    }

    /**
     * "mean,sd,ci95" rounded for CSV output.
     */
    protected function csvSummary(array $summary, int $decimals = 2): string
    {
        return implode(',', [
            round($summary['mean'], $decimals),
            round($summary['sd'], $decimals),
            round($summary['ci95'], $decimals),
        ]);
    }
}
