<?php

namespace Tests\Unit;

use App\Services\Recommendation\GowerDistance;
use PHPUnit\Framework\TestCase;

class GowerDistanceTest extends TestCase
{
    private const FEATURES = [
        'type'  => ['type' => GowerDistance::CATEGORICAL],
        'seats' => ['type' => GowerDistance::NUMERIC],
        'size'  => ['type' => GowerDistance::NUMERIC],
    ];

    public function test_identical_records_are_zero_apart()
    {
        $gower = new GowerDistance(self::FEATURES, ['seats' => 100, 'size' => 2]);
        $a     = ['type' => 'cafe', 'seats' => 40, 'size' => 2];

        $this->assertSame(0.0, $gower->distance($a, $a));
    }

    public function test_mixed_features_average_their_partials()
    {
        $gower = new GowerDistance(self::FEATURES, ['seats' => 100, 'size' => 2]);

        // type differs (1), seats 40 vs 90 over range 100 (0.5), size 1 vs 3 over range 2 (1)
        $distance = $gower->distance(
            ['type' => 'cafe', 'seats' => 40, 'size' => 1],
            ['type' => 'bakery', 'seats' => 90, 'size' => 3]
        );

        $this->assertEqualsWithDelta((1 + 0.5 + 1) / 3, $distance, 1e-9);
    }

    public function test_missing_values_are_left_out_of_both_sides_of_the_mean()
    {
        $gower = new GowerDistance(self::FEATURES, ['seats' => 100, 'size' => 2]);

        // Only type and size are comparable: (0 + 0.5) / 2
        $distance = $gower->distance(
            ['type' => 'cafe', 'seats' => null, 'size' => 1],
            ['type' => 'cafe', 'seats' => 70, 'size' => 2]
        );

        $this->assertEqualsWithDelta(0.25, $distance, 1e-9);
    }

    public function test_nothing_comparable_is_unknown_not_zero()
    {
        $gower = new GowerDistance(self::FEATURES, ['seats' => 100, 'size' => 2]);

        $this->assertNull($gower->distance(['type' => null], ['seats' => 10]));
    }

    public function test_weights_scale_each_feature()
    {
        $gower = new GowerDistance([
            'type'  => ['type' => GowerDistance::CATEGORICAL, 'weight' => 3],
            'seats' => ['type' => GowerDistance::NUMERIC, 'weight' => 1],
        ], ['seats' => 100]);

        // type differs (1 x 3), seats identical (0 x 1): 3 / 4
        $distance = $gower->distance(['type' => 'a', 'seats' => 10], ['type' => 'b', 'seats' => 10]);

        $this->assertEqualsWithDelta(0.75, $distance, 1e-9);
    }

    public function test_ranges_come_from_the_population_and_ignore_nulls()
    {
        $ranges = GowerDistance::rangesFor(self::FEATURES, [
            ['seats' => 20, 'size' => 1],
            ['seats' => 120, 'size' => null],
            ['seats' => null, 'size' => 1],
        ]);

        $this->assertSame(100.0, $ranges['seats']);
        $this->assertSame(0.0, $ranges['size']); // a single distinct value
        $this->assertArrayNotHasKey('type', $ranges);
    }

    public function test_a_zero_range_feature_counts_as_agreement()
    {
        $gower = new GowerDistance(self::FEATURES, ['seats' => 0, 'size' => 0]);

        $this->assertSame(0.0, $gower->distance(['seats' => 30], ['seats' => 30]));
    }

    public function test_unknown_feature_type_is_rejected()
    {
        $this->expectException(\InvalidArgumentException::class);

        new GowerDistance(['x' => ['type' => 'ordinalish']], []);
    }
}
