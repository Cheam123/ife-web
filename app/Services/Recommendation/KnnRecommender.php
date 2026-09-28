<?php

namespace App\Services\Recommendation;

/**
 * k-nearest-neighbour product recommender over outlets.
 *
 * For a target outlet it finds the k most similar outlets that have order
 * history (Gower distance over the outlet profile), then asks: what do those
 * neighbours buy that the target does not, or buys too little of?
 *
 *  - support  = share of neighbour similarity behind a product:
 *               sum(w_i for neighbours buying it) / sum(w_i), w_i = 1 - d_i
 *  - quantity = similarity-weighted mean of the buyers' monthly quantity,
 *               each scaled to the target's size (target scale / neighbour
 *               scale, clamped to [0.5, 2]) so a 20-seat cafe is not told to
 *               order like a 120-seat restaurant
 *  - status   = "gap"    the target has not bought it in the lookback window
 *               "top_up" it buys less than TOP_UP_RATIO of the suggestion
 *    Products the target already buys enough of are not recommended.
 *  - value    = the extra monthly spend the suggestion represents
 *
 * Pure PHP over plain arrays: no database, so it is unit-testable and the
 * caller (RecommendationService) decides where the numbers come from.
 */
final class KnnRecommender
{
    public const TOP_UP_RATIO = 0.6;

    private GowerDistance|null $gower = null;

    /**
     * @param array<string, array{type: string, weight?: float}> $features Gower feature spec
     * @param int   $k          neighbours to use
     * @param int   $limit      most items returned
     * @param float $minSupport least support for a product to be recommended
     */
    public function __construct(
        private array $features,
        private int $k = 5,
        private int $limit = 10,
        private float $minSupport = 0.3,
    ) {
    }

    /**
     * @param array $target     ['id', 'features' => [...], 'scale' => ?float, 'purchases' => [product_id => monthly qty]]
     * @param array $candidates list of outlets in the same shape
     * @param array $products   product_id => ['sku', 'name', 'unit', 'unit_price', 'category'] (active only)
     * @return array{items: array, neighbors: array<int, float>, gap_count: int, estimated_monthly_value: float}
     */
    public function recommend(array $target, array $candidates, array $products): array
    {
        $empty = ['items' => [], 'neighbors' => [], 'gap_count' => 0, 'estimated_monthly_value' => 0.0];

        $population  = array_merge([$target], $candidates);
        $this->gower = GowerDistance::fromRecords(
            $this->features,
            array_map(fn ($outlet) => $outlet['features'] ?? [], $population)
        );

        $neighbors = $this->nearest($target, $candidates);
        if (empty($neighbors)) {
            return $empty;
        }

        $totalWeight = array_sum(array_column($neighbors, 'weight'));
        $current     = $target['purchases'] ?? [];
        $items       = [];

        foreach ($this->productIds($neighbors, $products) as $productId) {
            $buyerWeight = 0.0;
            $weightedQty = 0.0;
            $buyers      = 0;

            foreach ($neighbors as $neighbor) {
                $qty = (float) ($neighbor['purchases'][$productId] ?? 0);
                if ($qty <= 0) {
                    continue;
                }
                $buyers++;
                $buyerWeight += $neighbor['weight'];
                $weightedQty += $neighbor['weight'] * $qty * $this->scaleFactor($target, $neighbor);
            }

            $support = $buyerWeight / $totalWeight;
            if ($buyers === 0 || $support < $this->minSupport) {
                continue;
            }

            $suggested = max(1.0, round($weightedQty / $buyerWeight));
            $has       = (float) ($current[$productId] ?? 0);

            if ($has <= 0) {
                $status = 'gap';
                $extra  = $suggested;
            } elseif ($has < self::TOP_UP_RATIO * $suggested) {
                $status = 'top_up';
                $extra  = $suggested - $has;
            } else {
                continue; // already buys enough of it
            }

            $product = $products[$productId];
            $price   = (float) ($product['unit_price'] ?? 0);

            $items[] = [
                'product_id'         => (int) $productId,
                'sku'                => $product['sku'] ?? null,
                'name'               => $product['name'] ?? null,
                'category'           => $product['category'] ?? null,
                'unit'               => $product['unit'] ?? null,
                'unit_price'         => round($price, 2),
                'status'             => $status,
                'support'            => round($support, 2),
                'buyers'             => $buyers,
                'neighbors_used'     => count($neighbors),
                'recommended_qty'    => $suggested,
                'current_qty'        => round($has, 1),
                'est_monthly_value'  => round($extra * $price, 2),
            ];
        }

        // Most-backed first; among equals, the bigger opportunity; then by id
        // so the order is stable between runs.
        usort($items, fn ($a, $b) => [$b['support'], $b['est_monthly_value'], $a['product_id']]
                                  <=> [$a['support'], $a['est_monthly_value'], $b['product_id']]);
        $items = array_slice($items, 0, $this->limit);

        $neighborDistances = [];
        foreach ($neighbors as $neighbor) {
            $neighborDistances[$neighbor['id']] = round($neighbor['distance'], 4);
        }

        return [
            'items'                   => $items,
            'neighbors'               => $neighborDistances,
            'gap_count'               => count(array_filter($items, fn ($item) => $item['status'] === 'gap')),
            'estimated_monthly_value' => round(array_sum(array_column($items, 'est_monthly_value')), 2),
        ];
    }

    /**
     * The k closest candidates that have purchases, closest first, each with
     * its Gower distance and similarity weight (1 - distance).
     */
    private function nearest(array $target, array $candidates): array
    {
        $scored = [];

        foreach ($candidates as $candidate) {
            if (($candidate['id'] ?? null) === ($target['id'] ?? null) || empty($candidate['purchases'])) {
                continue;
            }

            $distance = $this->gower->distance($target['features'] ?? [], $candidate['features'] ?? []);
            if ($distance === null || $distance >= 1.0) {
                continue; // nothing in common, so nothing to learn from
            }

            $scored[] = $candidate + ['distance' => $distance, 'weight' => 1.0 - $distance];
        }

        usort($scored, fn ($a, $b) => [$a['distance'], $a['id']] <=> [$b['distance'], $b['id']]);

        return array_slice($scored, 0, $this->k);
    }

    /** Active products any neighbour buys. */
    private function productIds(array $neighbors, array $products): array
    {
        $ids = [];
        foreach ($neighbors as $neighbor) {
            foreach (array_keys($neighbor['purchases'] ?? []) as $productId) {
                if (isset($products[$productId])) {
                    $ids[$productId] = true;
                }
            }
        }

        $ids = array_keys($ids);
        sort($ids);

        return $ids;
    }

    /** Target size relative to the neighbour's, clamped so one odd profile cannot run away. */
    private function scaleFactor(array $target, array $neighbor): float
    {
        $mine   = $target['scale'] ?? null;
        $theirs = $neighbor['scale'] ?? null;

        if (!is_numeric($mine) || !is_numeric($theirs) || (float) $theirs <= 0 || (float) $mine <= 0) {
            return 1.0;
        }

        return min(2.0, max(0.5, (float) $mine / (float) $theirs));
    }
}
