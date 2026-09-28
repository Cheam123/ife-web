<?php

namespace Tests\Unit;

use App\Services\Ai\ClaudeClient;
use App\Services\DailyDigestService;
use App\Services\DashboardService;
use App\Services\Recommendation\RecommendationService;
use App\Services\TaskRisk;
use PHPUnit\Framework\TestCase;

/**
 * The text around the AI features that must work without AI: parsing
 * Claude's recommendation explanation, and the template fallbacks for the
 * explanation and the daily digest.
 */
class AiTextTest extends TestCase
{
    private function recommendations(): RecommendationService
    {
        return new RecommendationService(new ClaudeClient());
    }

    public function test_explanation_reply_is_split_into_why_and_opening_line()
    {
        [$why, $line] = $this->recommendations()->parseExplanation(
            "WHY:\nSimilar cafes order oat milk every month.\n\nOPENING LINE:\n\"Have you thought about oat milk for the weekend crowd?\""
        );

        $this->assertSame('Similar cafes order oat milk every month.', $why);
        $this->assertSame('Have you thought about oat milk for the weekend crowd?', $line);
    }

    public function test_unstructured_reply_is_kept_as_the_explanation()
    {
        [$why, $line] = $this->recommendations()->parseExplanation('Similar outlets buy these.');

        $this->assertSame('Similar outlets buy these.', $why);
        $this->assertNull($line);
    }

    public function test_template_explanation_names_the_products_and_value()
    {
        [$why, $line] = $this->recommendations()->templateExplanation([
            'outlet'                  => ['type' => 'Cafe', 'segment' => 'Premium', 'size_band' => 'Small', 'seats' => 24],
            'similar_outlets'         => 5,
            'currency'                => 'RM',
            'gap_count'               => 2,
            'estimated_monthly_value' => 520.5,
            'recommended'             => [
                ['product' => 'Oat Milk'],
                ['product' => 'Caramel Syrup'],
            ],
        ]);

        $this->assertStringContainsString('5 most similar outlets', $why);
        $this->assertStringContainsString('Oat Milk and Caramel Syrup', $why);
        $this->assertStringContainsString('has not ordered any of them recently', $why);
        $this->assertStringContainsString('RM 520.50', $why);
        $this->assertStringStartsWith('Other cafe outlets like yours have been doing well with Oat Milk.', $line);
    }

    public function test_template_digest_has_every_section()
    {
        $digests = new DailyDigestService(new DashboardService(new TaskRisk()), new ClaudeClient());

        $text = $digests->template([
            'tasks'     => ['open' => 8, 'overdue' => 2, 'at_risk' => 3, 'done_7d' => 4, 'created_7d' => 6, 'completed_7d' => 1, 'new' => 3, 'in_progress' => 5, 'due_today' => 1],
            'forms'     => ['submitted_7d' => 5, 'pending' => 2, 'approved' => 3, 'rejected' => 0],
            'visits'    => ['last_7d' => 9, 'today' => 1],
            'orders'    => ['month_count' => 4, 'month_value' => 3200.0, 'currency' => 'RM'],
            'attention' => [
                ['id' => 7, 'reference' => 'T-20260928-0007', 'title' => 'Recalibrate grinder', 'lead' => 'Daily Grind', 'subscriber' => 'Aiman', 'reason' => 'Overdue'],
            ],
            'team'      => [
                ['name' => 'Aiman', 'open' => 4, 'overdue' => 1, 'at_risk' => 1, 'done_7d' => 2, 'visits_7d' => 5, 'forms_7d' => 1, 'orders_7d' => 0],
            ],
            'trend_7d'  => [],
        ]);

        foreach (['Summary:', 'Focus alerts:', 'Team:', 'Focus today:'] as $label) {
            $this->assertStringContainsString($label, $text);
        }
        $this->assertStringContainsString('Good morning. The team has 8 open tasks: 2 overdue and 3 at risk.', $text);
        $this->assertStringContainsString('- Recalibrate grinder at Daily Grind (Aiman): overdue.', $text);
        $this->assertStringContainsString('RM 3,200.00', $text);
        $this->assertStringContainsString('- Finish or reschedule the 2 overdue tasks.', $text);
    }
}
