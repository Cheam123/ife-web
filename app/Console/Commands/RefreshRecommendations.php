<?php

namespace App\Console\Commands;

use App\Services\Recommendation\RecommendationService;
use Illuminate\Console\Command;

class RefreshRecommendations extends Command
{
    protected $signature = 'recommendation:refresh {--lead=* : Only these lead ids (default: every outlet)}';

    protected $description = 'Recompute KNN product recommendations for outlets';

    public function handle(RecommendationService $recommendations): int
    {
        $leadIds = array_filter(array_map('intval', (array) $this->option('lead')));
        $written = $recommendations->refresh($leadIds ?: null);

        $this->info("Recommendations refreshed for {$written} outlet(s).");

        return self::SUCCESS;
    }
}
