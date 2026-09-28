<?php

namespace App\Services\Recommendation;

/**
 * Gower distance between two records with mixed feature types.
 *
 * Each feature contributes a partial distance in [0, 1]:
 *  - numeric (and ordinal, passed as ranks): |a - b| / range, where range is
 *    that feature's spread across the whole population. A zero range (every
 *    value the same) makes the feature agree everywhere.
 *  - categorical: 0 when the values are equal, 1 otherwise.
 *
 * The distance is the weighted mean of the partials over the features both
 * records have. A feature that is null on either side is left out of the
 * numerator and the denominator alike, which is how Gower handles missing
 * data. When no feature can be compared the distance is null (unknown), not
 * 0 or 1.
 */
final class GowerDistance
{
    public const NUMERIC     = 'numeric';
    public const CATEGORICAL = 'categorical';

    /** @var array<string, array{type: string, weight: float}> */
    private array $features;

    /** @var array<string, float> numeric feature => max - min */
    private array $ranges;

    /**
     * @param array<string, array{type: string, weight?: float}> $features
     * @param array<string, float> $ranges numeric feature ranges, see rangesFor()
     */
    public function __construct(array $features, array $ranges)
    {
        foreach ($features as $name => $spec) {
            $type = $spec['type'] ?? self::CATEGORICAL;
            if (!in_array($type, [self::NUMERIC, self::CATEGORICAL], true)) {
                throw new \InvalidArgumentException("Unknown Gower feature type for {$name}: {$type}");
            }
            $this->features[$name] = [
                'type'   => $type,
                'weight' => max(0.0, (float) ($spec['weight'] ?? 1.0)),
            ];
        }

        $this->ranges = $ranges;
    }

    /**
     * Builds a calculator whose numeric ranges come from $records.
     *
     * @param array<string, array{type: string, weight?: float}> $features
     * @param array<int, array<string, mixed>> $records feature name => value
     */
    public static function fromRecords(array $features, array $records): self
    {
        return new self($features, self::rangesFor($features, $records));
    }

    /**
     * Spread (max - min) of every numeric feature across the records,
     * ignoring nulls. A feature with fewer than two values has range 0.
     *
     * @return array<string, float>
     */
    public static function rangesFor(array $features, array $records): array
    {
        $ranges = [];

        foreach ($features as $name => $spec) {
            if (($spec['type'] ?? self::CATEGORICAL) !== self::NUMERIC) {
                continue;
            }

            $values = [];
            foreach ($records as $record) {
                $value = $record[$name] ?? null;
                if (is_numeric($value)) {
                    $values[] = (float) $value;
                }
            }

            $ranges[$name] = count($values) < 2 ? 0.0 : max($values) - min($values);
        }

        return $ranges;
    }

    /**
     * @param array<string, mixed> $a feature name => value
     * @param array<string, mixed> $b feature name => value
     * @return float|null distance in [0, 1]; null when nothing is comparable
     */
    public function distance(array $a, array $b): ?float
    {
        $weighted = 0.0;
        $weights  = 0.0;

        foreach ($this->features as $name => $spec) {
            $left  = $a[$name] ?? null;
            $right = $b[$name] ?? null;

            if ($left === null || $left === '' || $right === null || $right === '' || $spec['weight'] <= 0) {
                continue;
            }

            if ($spec['type'] === self::NUMERIC) {
                if (!is_numeric($left) || !is_numeric($right)) {
                    continue;
                }
                $range   = $this->ranges[$name] ?? 0.0;
                $partial = $range > 0 ? min(1.0, abs((float) $left - (float) $right) / $range) : 0.0;
            } else {
                $partial = (string) $left === (string) $right ? 0.0 : 1.0;
            }

            $weighted += $spec['weight'] * $partial;
            $weights  += $spec['weight'];
        }

        return $weights > 0 ? $weighted / $weights : null;
    }
}
