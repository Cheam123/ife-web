<?php

namespace Tests\Unit;

use App\Services\TaskRisk;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\TestCase;

class TaskRiskTest extends TestCase
{
    private TaskRisk $risk;
    private Carbon $now;

    protected function setUp(): void
    {
        parent::setUp();
        $this->risk = new TaskRisk(24, 0.75);
        $this->now  = Carbon::parse('2026-09-28 10:00:00');
    }

    public function test_past_due_is_overdue()
    {
        $result = $this->risk->assess($this->now->copy()->subDays(2), $this->now->copy()->subHour(), $this->now);

        $this->assertSame(TaskRisk::OVERDUE, $result['level']);
        $this->assertSame('Overdue', $result['reason']);
    }

    public function test_due_within_the_window_is_at_risk()
    {
        $result = $this->risk->assess($this->now->copy()->subHour(), $this->now->copy()->addHours(5), $this->now);

        $this->assertSame(TaskRisk::AT_RISK, $result['level']);
        $this->assertSame('Due within 5 h', $result['reason']);
    }

    public function test_most_of_the_window_used_is_at_risk_even_when_due_is_days_away()
    {
        // 10 days in, 2 days left: 83% used.
        $result = $this->risk->assess($this->now->copy()->subDays(10), $this->now->copy()->addDays(2), $this->now);

        $this->assertSame(TaskRisk::AT_RISK, $result['level']);
        $this->assertSame('83% of the time used', $result['reason']);
    }

    public function test_plenty_of_time_left_is_on_track()
    {
        $result = $this->risk->assess($this->now->copy()->subDay(), $this->now->copy()->addDays(5), $this->now);

        $this->assertSame(TaskRisk::ON_TRACK, $result['level']);
        $this->assertNull($result['reason']);
    }

    public function test_no_due_date_is_on_track()
    {
        $this->assertSame(TaskRisk::ON_TRACK, $this->risk->assess($this->now, null, $this->now)['level']);
    }

    public function test_combine_fills_a_missing_time()
    {
        $this->assertSame('2026-09-30 23:59:59', TaskRisk::combine('2026-09-30', null, '23:59:59')->toDateTimeString());
        $this->assertSame('2026-09-30 09:30:00', TaskRisk::combine('2026-09-30', '09:30:00', '23:59:59')->toDateTimeString());
        $this->assertNull(TaskRisk::combine(null, '09:30:00', '23:59:59'));
    }
}
