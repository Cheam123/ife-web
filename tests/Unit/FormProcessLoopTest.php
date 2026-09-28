<?php

namespace Tests\Unit;

use App\Services\FormProcessService;
use App\Services\FormSchemaService;
use PHPUnit\Framework\TestCase;

/**
 * Loop branches, runtime-evaluated conditions and runtime-assigned handlers:
 * validator rules + the cursor-based materializeFrom() walk.
 */
class FormProcessLoopTest extends TestCase
{
    private FormProcessService $service;
    private FormSchemaService $schemaService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service       = new FormProcessService();
        $this->schemaService = new FormSchemaService();
    }

    private function schema(): array
    {
        return $this->schemaService->normalize([
            'schema_version' => 2,
            'groups'         => [],
            'elements'       => [
                ['id' => 'el_status', 'kind' => 'field', 'type' => 'select', 'label' => 'Status',
                 'options' => ['Pending', 'Complete']],
                ['id' => 'el_note', 'kind' => 'field', 'type' => 'text', 'label' => 'Note'],
            ],
        ]);
    }

    /**
     * Final handler fills Status; if Status is Pending, a runtime-assigned
     * follow-up handler loops until it is Complete.
     */
    private function loopDefinition(?array $armNodes = null): array
    {
        return ['process_version' => 1, 'nodes' => [
            ['id' => 'nd_a', 'type' => 'fill', 'name' => 'Final Section', 'assignee_ids' => [7],
             'field_permissions' => ['default' => 'read', 'overrides' => ['el_status' => 'edit']]],
            ['id' => 'nd_br', 'type' => 'branch', 'branches' => [
                ['id' => 'br_pending', 'name' => 'Still pending', 'loop' => true,
                 'when' => ['logic' => 'or', 'groups' => [['logic' => 'and', 'conditions' => [
                     ['field' => 'el_status', 'operator' => 'equals', 'value' => 'Pending'],
                 ]]]],
                 'nodes' => $armNodes ?? [
                     ['id' => 'nd_fix', 'type' => 'fill', 'name' => 'Follow-up', 'assignee_mode' => 'runtime',
                      'assignee_ids' => [],
                      'field_permissions' => ['default' => 'read', 'overrides' => ['el_status' => 'edit']]],
                 ]],
                ['id' => 'br_done', 'name' => 'Complete', 'when' => null, 'nodes' => []],
            ]],
        ]];
    }

    private function deferredIds(array $definition): array
    {
        return $this->service->deferredFieldIds($definition, $this->schema(), $this->schemaService);
    }

    /* ================================================================
     * Validator rules
     * ================================================================ */

    public function test_validator_accepts_a_well_formed_loop_arm()
    {
        $errors = $this->service->validateDefinition(
            $this->loopDefinition(), ['el_status' => true, 'el_note' => true], $this->schemaService, $this->schema()
        );

        $this->assertSame([], $errors);
    }

    public function test_validator_rejects_loop_on_the_default_branch()
    {
        $definition = $this->loopDefinition();
        $definition['nodes'][1]['branches'][1]['loop'] = true;

        $errors = $this->service->validateDefinition(
            $definition, ['el_status' => true, 'el_note' => true], $this->schemaService, $this->schema()
        );

        $this->assertNotEmpty(array_filter($errors, fn ($e) => str_contains($e, 'default branch cannot repeat')));
    }

    public function test_validator_rejects_loop_arm_without_a_blocking_step()
    {
        $definition = $this->loopDefinition([
            ['id' => 'nd_cc', 'type' => 'cc', 'name' => 'Notify', 'user_ids' => [3]],
        ]);

        $errors = $this->service->validateDefinition(
            $definition, ['el_status' => true, 'el_note' => true], $this->schemaService, $this->schema()
        );

        $this->assertNotEmpty(array_filter($errors, fn ($e) => str_contains($e, 'at least one Handler or Approval step')));
    }

    public function test_validator_rejects_loop_arm_that_cannot_change_its_condition()
    {
        // The follow-up handler may only edit el_note, never el_status — the
        // loop condition could never stop matching.
        $definition = $this->loopDefinition([
            ['id' => 'nd_fix', 'type' => 'fill', 'name' => 'Follow-up', 'assignee_ids' => [8],
             'field_permissions' => ['default' => 'read', 'overrides' => ['el_note' => 'edit']]],
        ]);

        $errors = $this->service->validateDefinition(
            $definition, ['el_status' => true, 'el_note' => true], $this->schemaService, $this->schema()
        );

        $this->assertNotEmpty(array_filter($errors, fn ($e) => str_contains($e, 'can fill the field(s) its condition checks')));
    }

    public function test_validator_allows_runtime_assignee_handler_without_assignees()
    {
        $errors = $this->service->validateDefinition(
            $this->loopDefinition(), ['el_status' => true, 'el_note' => true], $this->schemaService, $this->schema()
        );
        $this->assertSame([], $errors);

        // ...but a fixed handler still needs people.
        $definition = $this->loopDefinition();
        $definition['nodes'][0]['assignee_ids'] = [];
        $errors = $this->service->validateDefinition(
            $definition, ['el_status' => true, 'el_note' => true], $this->schemaService, $this->schema()
        );
        $this->assertNotEmpty(array_filter($errors, fn ($e) => str_contains($e, 'needs at least one handler')));
    }

    public function test_validator_rejects_unknown_assignee_mode()
    {
        $definition = $this->loopDefinition();
        $definition['nodes'][0]['assignee_mode'] = 'whoever';

        $errors = $this->service->validateDefinition(
            $definition, ['el_status' => true, 'el_note' => true], $this->schemaService, $this->schema()
        );

        $this->assertNotEmpty(array_filter($errors, fn ($e) => str_contains($e, 'invalid assignee mode')));
    }

    /* ================================================================
     * materializeFrom(): static flows resolve in one walk
     * ================================================================ */

    public function test_static_definition_materializes_fully_in_one_walk()
    {
        $definition = ['process_version' => 1, 'nodes' => [
            ['id' => 'nd_mgr', 'type' => 'approval', 'name' => 'Manager', 'approver_ids' => [5],
             'approval_mode' => 'any', 'field_permissions' => ['default' => 'read']],
            ['id' => 'nd_br', 'type' => 'branch', 'branches' => [
                ['id' => 'br_big', 'name' => 'Big',
                 'when' => ['logic' => 'or', 'groups' => [['logic' => 'and', 'conditions' => [
                     ['field' => 'el_note', 'operator' => 'equals', 'value' => 'big'],
                 ]]]],
                 'nodes' => [
                     ['id' => 'nd_hr', 'type' => 'approval', 'name' => 'HR', 'approver_ids' => [9],
                      'approval_mode' => 'any', 'field_permissions' => ['default' => 'read']],
                 ]],
                ['id' => 'br_default', 'name' => 'Default', 'when' => null, 'nodes' => []],
            ]],
            ['id' => 'nd_cc', 'type' => 'cc', 'name' => 'Notify', 'user_ids' => [30]],
        ]];

        // el_note is a submitter field (not deferred): the branch is static and
        // the whole chain materializes at submit even though nothing is done.
        $result = $this->service->materializeFrom($definition, [], ['el_note' => 'big'], [], true, []);

        $this->assertSame(['nd_mgr', 'nd_hr', 'nd_cc'], array_column($result['steps'], 'node_id'));
        $this->assertTrue($result['complete']);
        $this->assertNull($result['needs']);
        $this->assertSame('br_big', $result['trail'][0]['matched']);
        $this->assertSame([1, 1, 1], array_column($result['steps'], 'iteration'));
    }

    /* ================================================================
     * materializeFrom(): runtime branch + loop + runtime assignee
     * ================================================================ */

    public function test_full_loop_lifecycle()
    {
        $definition = $this->loopDefinition();
        $deferred   = $this->deferredIds($definition);
        $this->assertSame(['el_status'], $deferred);

        // 1. Submit: handler A materializes, walk parks at the runtime branch.
        $r1 = $this->service->materializeFrom($definition, [], ['el_status' => null], $deferred, true, []);
        $this->assertSame(['nd_a'], array_column($r1['steps'], 'node_id'));
        $this->assertFalse($r1['complete']);
        $this->assertNull($r1['needs']);
        $this->assertSame([], $r1['trail']);

        // 2. A completed with Status=Pending but no pick: pause for an assignee.
        $r2 = $this->service->materializeFrom($definition, $r1['cursor'], ['el_status' => 'Pending'], $deferred, true, []);
        $this->assertSame([], $r2['steps']);
        $this->assertSame('assignee', $r2['needs']['type']);
        $this->assertSame('nd_fix', $r2['needs']['node_id']);
        $this->assertSame('Follow-up', $r2['needs']['name']);

        // 3. Retry with a pick: round-1 follow-up row for user 8.
        $r3 = $this->service->materializeFrom($definition, $r1['cursor'], ['el_status' => 'Pending'], $deferred, true, [8]);
        $this->assertSame(['nd_fix'], array_column($r3['steps'], 'node_id'));
        $this->assertSame([8], $r3['steps'][0]['approver_ids']);
        $this->assertTrue($r3['steps'][0]['runtime_assigned']);
        $this->assertSame(1, $r3['steps'][0]['iteration']);
        $this->assertSame([['branch_node' => 'nd_br', 'matched' => 'br_pending', 'iteration' => 1]],
            array_map(fn ($t) => ['branch_node' => $t['branch_node'], 'matched' => $t['matched'], 'iteration' => $t['iteration']], $r3['trail']));
        $this->assertFalse($r3['complete']);

        // 4. Still Pending: the branch re-evaluates and round 2 starts for user 9.
        $r4 = $this->service->materializeFrom($definition, $r3['cursor'], ['el_status' => 'Pending'], $deferred, true, [9]);
        $this->assertSame(['nd_fix'], array_column($r4['steps'], 'node_id'));
        $this->assertSame([9], $r4['steps'][0]['approver_ids']);
        $this->assertSame(2, $r4['steps'][0]['iteration']);
        $this->assertSame(2, $r4['trail'][0]['iteration']);
        $this->assertFalse($r4['complete']);

        // 5. Status=Complete: default arm matches, the loop exits, flow ends.
        $r5 = $this->service->materializeFrom($definition, $r4['cursor'], ['el_status' => 'Complete'], $deferred, true, []);
        $this->assertSame([], $r5['steps']);
        $this->assertTrue($r5['complete']);
        $this->assertNull($r5['needs']);
        $this->assertSame('br_done', $r5['trail'][0]['matched']);
    }

    public function test_runtime_branch_waits_until_reached()
    {
        // Not reached (handler A still pending): the walk must not evaluate
        // the branch even though answers would match.
        $definition = $this->loopDefinition();
        $deferred   = $this->deferredIds($definition);
        $r1 = $this->service->materializeFrom($definition, [], ['el_status' => null], $deferred, true, []);

        $r2 = $this->service->materializeFrom($definition, $r1['cursor'], ['el_status' => 'Pending'], $deferred, false, [8]);

        $this->assertSame([], $r2['steps']);
        $this->assertSame([], $r2['trail']);
        $this->assertFalse($r2['complete']);
        $this->assertNull($r2['needs']);
        $this->assertSame($r1['cursor'], $r2['cursor']);
    }

    public function test_runaway_loop_is_forced_onto_the_default_arm()
    {
        // Degenerate hand-crafted arm (only a CC step, so nothing blocks and
        // nothing can change the answer): the guard must break the spin.
        $definition = $this->loopDefinition([
            ['id' => 'nd_cc', 'type' => 'cc', 'name' => 'Notify', 'user_ids' => [3]],
        ]);
        $deferred = $this->deferredIds($definition);
        $r1 = $this->service->materializeFrom($definition, [], ['el_status' => null], $deferred, true, []);

        $r2 = $this->service->materializeFrom($definition, $r1['cursor'], ['el_status' => 'Pending'], $deferred, true, []);

        $this->assertTrue($r2['complete']);
        $last = end($r2['trail']);
        $this->assertTrue($last['forced_default'] ?? false);
        $this->assertSame('br_done', $last['matched']);
        $this->assertLessThanOrEqual(FormProcessService::MAX_BRANCH_EVALS + 1, count($r2['trail']));
    }

    public function test_condition_field_ids_and_runtime_branch_detection()
    {
        $definition = $this->loopDefinition();
        $branch     = $definition['nodes'][1];

        $this->assertSame(['el_status'], $this->service->conditionFieldIds($branch['branches'][0]['when']));
        $this->assertTrue($this->service->isRuntimeBranch($branch, ['el_status' => 0]));

        // Same branch without loop and without deferred refs = static.
        $static = $branch;
        unset($static['branches'][0]['loop']);
        $this->assertFalse($this->service->isRuntimeBranch($static, []));
    }
}
