<?php

namespace Tests\Unit;

use App\Services\Recommendation\GowerDistance;
use App\Services\Recommendation\KnnRecommender;
use PHPUnit\Framework\TestCase;

class KnnRecommenderTest extends TestCase
{
    private const FEATURES = [
        'type'  => ['type' => GowerDistance::CATEGORICAL],
        'seats' => ['type' => GowerDistance::NUMERIC],
    ];

    private const PRODUCTS = [
        1 => ['sku' => 'BEAN', 'name' => 'Beans',    'unit' => 'kg',     'unit_price' => 80.0, 'category' => 'Coffee'],
        2 => ['sku' => 'OAT',  'name' => 'Oat Milk', 'unit' => 'litre',  'unit_price' => 14.0, 'category' => 'Dairy'],
        3 => ['sku' => 'SYR',  'name' => 'Syrup',    'unit' => 'bottle', 'unit_price' => 30.0, 'category' => 'Syrups'],
        4 => ['sku' => 'CHOC', 'name' => 'Choc',     'unit' => 'kg',     'unit_price' => 45.0, 'category' => 'Powders'],
    ];

    private function outlet(int $id, string $type, ?int $seats, array $purchases = []): array
    {
        return ['id' => $id, 'features' => ['type' => $type, 'seats' => $seats], 'scale' => $seats, 'purchases' => $purchases];
    }

    private function recommender(int $k = 3, float $minSupport = 0.3): KnnRecommender
    {
        return new KnnRecommender(self::FEATURES, $k, 10, $minSupport);
    }

    public function test_recommends_what_similar_outlets_buy_and_the_target_does_not()
    {
        $target     = $this->outlet(1, 'cafe', 40, [1 => 8]);
        $candidates = [
            $this->outlet(2, 'cafe', 40, [1 => 8, 2 => 10]),
            $this->outlet(3, 'cafe', 50, [1 => 9, 2 => 12]),
            $this->outlet(4, 'bakery', 20, [4 => 5]),
        ];

        $result = $this->recommender(2)->recommend($target, $candidates, self::PRODUCTS);

        $this->assertSame([2, 3], array_keys($result['neighbors'])); // the two cafes, closest first
        $this->assertCount(1, $result['items']);                      // beans already bought enough
        $item = $result['items'][0];
        $this->assertSame(2, $item['product_id']);
        $this->assertSame('gap', $item['status']);
        $this->assertSame(1.0, $item['support']);
        $this->assertSame(2, $item['buyers']);
        $this->assertSame(1, $result['gap_count']);
        $this->assertSame($item['recommended_qty'] * 14.0, $item['est_monthly_value']);
    }

    public function test_quantities_scale_to_the_target_size_within_limits()
    {
        // Neighbour has 100 seats and buys 20; a 50-seat target should get ~10.
        $target    = $this->outlet(1, 'cafe', 50);
        $neighbour = $this->outlet(2, 'cafe', 100, [2 => 20]);

        $item = $this->recommender(1)->recommend($target, [$neighbour], self::PRODUCTS)['items'][0];
        $this->assertEquals(10, $item['recommended_qty']);

        // A 10-seat target is clamped to half, not a tenth.
        $tiny = $this->outlet(1, 'cafe', 10);
        $item = $this->recommender(1)->recommend($tiny, [$neighbour], self::PRODUCTS)['items'][0];
        $this->assertEquals(10, $item['recommended_qty']);
    }

    public function test_under_ordering_becomes_a_top_up_and_values_only_the_difference()
    {
        $target     = $this->outlet(1, 'cafe', 40, [2 => 2]);
        $candidates = [$this->outlet(2, 'cafe', 40, [2 => 10])];

        $item = $this->recommender(1)->recommend($target, $candidates, self::PRODUCTS)['items'][0];

        $this->assertSame('top_up', $item['status']);
        $this->assertEquals(10, $item['recommended_qty']);
        $this->assertEquals((10 - 2) * 14.0, $item['est_monthly_value']);
    }

    public function test_support_below_the_threshold_is_not_recommended()
    {
        $target     = $this->outlet(1, 'cafe', 40);
        $candidates = [
            $this->outlet(2, 'cafe', 40, [1 => 5]),
            $this->outlet(3, 'cafe', 40, [1 => 5]),
            $this->outlet(4, 'cafe', 40, [1 => 5, 3 => 2]), // only 1 of 3 buys syrup
        ];

        $items = $this->recommender(3, 0.5)->recommend($target, $candidates, self::PRODUCTS)['items'];

        $this->assertSame([1], array_column($items, 'product_id'));
    }

    public function test_inactive_products_and_outlets_without_purchases_are_ignored()
    {
        $target     = $this->outlet(1, 'cafe', 40);
        $candidates = [
            $this->outlet(2, 'cafe', 40, [99 => 5, 2 => 4]), // 99 not in the active catalogue
            $this->outlet(3, 'cafe', 40, []),                 // prospect: nothing to learn from
        ];

        $result = $this->recommender(3)->recommend($target, $candidates, self::PRODUCTS);

        $this->assertSame([2], array_keys($result['neighbors']));
        $this->assertSame([2], array_column($result['items'], 'product_id'));
    }

    public function test_no_neighbours_gives_an_empty_result()
    {
        $result = $this->recommender()->recommend($this->outlet(1, 'cafe', 40), [], self::PRODUCTS);

        $this->assertSame([], $result['items']);
        $this->assertSame(0, $result['gap_count']);
        $this->assertSame(0.0, $result['estimated_monthly_value']);
    }

    public function test_the_target_is_never_its_own_neighbour()
    {
        $target = $this->outlet(1, 'cafe', 40, [1 => 5]);

        $result = $this->recommender()->recommend($target, [$target], self::PRODUCTS);

        $this->assertSame([], $result['neighbors']);
    }

    public function test_ranking_is_by_support_then_value()
    {
        $target     = $this->outlet(1, 'cafe', 40);
        $candidates = [
            $this->outlet(2, 'cafe', 40, [2 => 10, 3 => 10, 4 => 1]),
            $this->outlet(3, 'cafe', 40, [2 => 10, 3 => 10]),
        ];

        $items = $this->recommender(2)->recommend($target, $candidates, self::PRODUCTS)['items'];

        // Oat and syrup have full support; syrup is worth more (10 x 30 > 10 x 14).
        // Choc has half support, so it comes last.
        $this->assertSame([3, 2, 4], array_column($items, 'product_id'));
    }
}
