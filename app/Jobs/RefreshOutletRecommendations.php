<?php

namespace App\Jobs;

use App\Services\Recommendation\RecommendationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Recomputes the recommendations of specific outlets after their profile or
 * orders change. Other outlets that count them as a neighbour catch up in
 * the nightly recommendation:refresh.
 *
 * Runs after the surrounding transaction commits, and never throws: a
 * recommendation refresh must not break saving an order or an outlet.
 */
class RefreshOutletRecommendations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;

    /** @param int[] $leadIds */
    public function __construct(public array $leadIds)
    {
        $this->afterCommit = true;
    }

    public function handle(RecommendationService $recommendations): void
    {
        try {
            $recommendations->refresh($this->leadIds);
        } catch (\Throwable $e) {
            Log::warning('RefreshOutletRecommendations failed', [
                'lead_ids' => $this->leadIds,
                'error'    => $e->getMessage(),
            ]);
        }
    }
}
