<?php

namespace Tests\Unit;

use App\Services\FormConditionEvaluator;
use PHPUnit\Framework\TestCase;

class FormConditionEvaluatorTest extends TestCase
{
    private FormConditionEvaluator $evaluator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluator = new FormConditionEvaluator();
    }

    private function single(string $field, string $operator, $value = null): array
    {
        return [
            'logic'  => 'or',
            'groups' => [
                ['logic' => 'and', 'conditions' => [
                    ['field' => $field, 'operator' => $operator, 'value' => $value],
                ]],
            ],
        ];
    }

    public function test_null_schema_is_always_true()
    {
        $this->assertTrue($this->evaluator->evaluate(null, []));
        $this->assertTrue($this->evaluator->evaluate([], ['a' => 'x']));
        $this->assertTrue($this->evaluator->evaluate(['logic' => 'or', 'groups' => []], []));
    }

    public function test_equals_and_not_equals()
    {
        $this->assertTrue($this->evaluator->evaluate($this->single('a', 'equals', 'VIP'), ['a' => 'VIP']));
        $this->assertFalse($this->evaluator->evaluate($this->single('a', 'equals', 'VIP'), ['a' => 'Walk-in']));
        $this->assertFalse($this->evaluator->evaluate($this->single('a', 'equals', 'VIP'), ['a' => null]));
        $this->assertFalse($this->evaluator->evaluate($this->single('a', 'equals', 'VIP'), []));

        $this->assertTrue($this->evaluator->evaluate($this->single('a', 'not_equals', 'VIP'), ['a' => 'Walk-in']));
        $this->assertTrue($this->evaluator->evaluate($this->single('a', 'not_equals', 'VIP'), ['a' => null]));
        $this->assertFalse($this->evaluator->evaluate($this->single('a', 'not_equals', 'VIP'), ['a' => 'VIP']));
    }

    public function test_includes_and_not_includes()
    {
        $this->assertTrue($this->evaluator->evaluate($this->single('a', 'includes', 'Delivery'), ['a' => ['Pickup', 'Delivery']]));
        $this->assertFalse($this->evaluator->evaluate($this->single('a', 'includes', 'Delivery'), ['a' => ['Pickup']]));
        $this->assertFalse($this->evaluator->evaluate($this->single('a', 'includes', 'Delivery'), ['a' => 'Delivery'])); // scalar is not a set
        $this->assertFalse($this->evaluator->evaluate($this->single('a', 'includes', 'Delivery'), []));

        $this->assertTrue($this->evaluator->evaluate($this->single('a', 'not_includes', 'Delivery'), ['a' => ['Pickup']]));
        $this->assertTrue($this->evaluator->evaluate($this->single('a', 'not_includes', 'Delivery'), []));
        $this->assertFalse($this->evaluator->evaluate($this->single('a', 'not_includes', 'Delivery'), ['a' => ['Delivery']]));
    }

    public function test_gt_lt_numeric_and_dates()
    {
        // Numeric compare (not lexicographic: "10" > "3").
        $this->assertTrue($this->evaluator->evaluate($this->single('n', 'gt', '3'), ['n' => '10']));
        $this->assertFalse($this->evaluator->evaluate($this->single('n', 'lt', '3'), ['n' => '10']));

        // ISO date strings compare correctly as strings.
        $this->assertTrue($this->evaluator->evaluate($this->single('d', 'gt', '2026-01-01'), ['d' => '2026-07-12']));
        $this->assertTrue($this->evaluator->evaluate($this->single('d', 'lt', '2026-12-31'), ['d' => '2026-07-12']));

        // Empty never satisfies gt/lt.
        $this->assertFalse($this->evaluator->evaluate($this->single('n', 'gt', '3'), []));
        $this->assertFalse($this->evaluator->evaluate($this->single('n', 'lt', '3'), []));
    }

    public function test_is_empty_and_is_not_empty()
    {
        $this->assertTrue($this->evaluator->evaluate($this->single('a', 'is_empty'), []));
        $this->assertTrue($this->evaluator->evaluate($this->single('a', 'is_empty'), ['a' => '']));
        $this->assertTrue($this->evaluator->evaluate($this->single('a', 'is_empty'), ['a' => []]));
        $this->assertTrue($this->evaluator->evaluate($this->single('a', 'is_empty'), ['a' => ['', null]]));
        $this->assertFalse($this->evaluator->evaluate($this->single('a', 'is_empty'), ['a' => '0']));

        $this->assertTrue($this->evaluator->evaluate($this->single('a', 'is_not_empty'), ['a' => 'x']));
        $this->assertFalse($this->evaluator->evaluate($this->single('a', 'is_not_empty'), ['a' => []]));
    }

    public function test_conditions_in_a_group_are_anded()
    {
        $schema = [
            'logic'  => 'or',
            'groups' => [
                ['logic' => 'and', 'conditions' => [
                    ['field' => 'a', 'operator' => 'equals', 'value' => 'x'],
                    ['field' => 'b', 'operator' => 'equals', 'value' => 'y'],
                ]],
            ],
        ];

        $this->assertTrue($this->evaluator->evaluate($schema, ['a' => 'x', 'b' => 'y']));
        $this->assertFalse($this->evaluator->evaluate($schema, ['a' => 'x', 'b' => 'z']));
        $this->assertFalse($this->evaluator->evaluate($schema, ['a' => 'z', 'b' => 'y']));
    }

    public function test_groups_are_ored()
    {
        $schema = [
            'logic'  => 'or',
            'groups' => [
                ['logic' => 'and', 'conditions' => [['field' => 'a', 'operator' => 'equals', 'value' => 'x']]],
                ['logic' => 'and', 'conditions' => [['field' => 'b', 'operator' => 'equals', 'value' => 'y']]],
            ],
        ];

        $this->assertTrue($this->evaluator->evaluate($schema, ['a' => 'x', 'b' => 'nope']));
        $this->assertTrue($this->evaluator->evaluate($schema, ['a' => 'nope', 'b' => 'y']));
        $this->assertFalse($this->evaluator->evaluate($schema, ['a' => 'nope', 'b' => 'nope']));
    }

    public function test_unknown_operator_fails_closed()
    {
        $this->assertFalse($this->evaluator->evaluate($this->single('a', 'wibble', 'x'), ['a' => 'x']));
    }

    public function test_empty_condition_group_never_matches()
    {
        $schema = ['logic' => 'or', 'groups' => [['logic' => 'and', 'conditions' => []]]];
        $this->assertFalse($this->evaluator->evaluate($schema, ['a' => 'x']));
    }

    public function test_is_empty_and_is_not_empty_on_a_gps_stamp()
    {
        $stamp = ['lat' => 3.1390123, 'lng' => 101.6868553, 'accuracy' => null, 'captured_at' => '2026-09-28T09:41:07+08:00'];

        $this->assertTrue($this->evaluator->evaluate($this->single('g', 'is_not_empty'), ['g' => $stamp]));
        $this->assertFalse($this->evaluator->evaluate($this->single('g', 'is_empty'), ['g' => $stamp]));

        // Null Island (0, 0) is a real place, not an empty answer.
        $this->assertTrue($this->evaluator->evaluate($this->single('g', 'is_not_empty'), ['g' => ['lat' => 0, 'lng' => 0]]));

        $this->assertTrue($this->evaluator->evaluate($this->single('g', 'is_empty'), ['g' => null]));
        $this->assertTrue($this->evaluator->evaluate($this->single('g', 'is_empty'), []));
        $this->assertFalse($this->evaluator->evaluate($this->single('g', 'is_not_empty'), ['g' => null]));
    }
}
