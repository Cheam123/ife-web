<?php

namespace Tests\Unit;

use App\Services\FormProcessService;
use App\Services\FormSchemaService;
use PHPUnit\Framework\TestCase;

/**
 * "Repeat from an earlier step" + handlers assigned from a Person field —
 * the reassignment workflow: Admin picks a technician, the technician fills
 * their section, Admin sets Status; while Status is Pending the flow rewinds
 * to the assignment step so a different technician can be appointed.
 */
class FormReassignLoopTest extends TestCase
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
                ['id' => 'el_request', 'kind' => 'field', 'type' => 'text', 'label' => 'Request'],
                // Required, because the Technician Section takes its handler from
                // it — an optional person field could arrive empty and leave the
                // step with nobody to go to (validateDefinition enforces this).
                ['id' => 'el_tech', 'kind' => 'field', 'type' => 'user', 'label' => 'Technician Name',
                 'mandatory' => true],
                ['id' => 'el_work', 'kind' => 'field', 'type' => 'text', 'label' => 'Work Done'],
                ['id' => 'el_status', 'kind' => 'field', 'type' => 'select', 'label' => 'Status',
                 'values' => ['Pending', 'Complete']],
            ],
        ]);
    }

    private function definition(): array
    {
        return ['process_version' => 1, 'nodes' => [
            ['id' => 'nd_assign', 'type' => 'fill', 'name' => 'Assign Technician', 'assignee_ids' => [1],
             'field_permissions' => ['default' => 'read', 'overrides' => ['el_tech' => 'edit']]],
            ['id' => 'nd_tech', 'type' => 'fill', 'name' => 'Technician Section',
             'assignee_mode' => 'field', 'assignee_field' => 'el_tech', 'assignee_ids' => [],
             'field_permissions' => ['default' => 'read', 'overrides' => ['el_work' => 'edit']]],
            ['id' => 'nd_admin', 'type' => 'fill', 'name' => 'Admin Review', 'assignee_ids' => [1],
             'field_permissions' => ['default' => 'read', 'overrides' => ['el_status' => 'edit']]],
            ['id' => 'nd_br', 'type' => 'branch', 'branches' => [
                ['id' => 'br_pending', 'name' => 'Still pending', 'loop_to' => 'nd_assign',
                 'when' => ['logic' => 'or', 'groups' => [['logic' => 'and', 'conditions' => [
                     ['field' => 'el_status', 'operator' => 'equals', 'value' => 'Pending'],
                 ]]]],
                 'nodes' => []],
                ['id' => 'br_done', 'name' => 'Complete', 'when' => null, 'nodes' => []],
            ]],
        ]];
    }

    private function validFieldIds(): array
    {
        return collect($this->schema()['elements'])->mapWithKeys(fn ($e) => [$e['id'] => true])->all();
    }

    private function deferred(): array
    {
        return $this->service->deferredFieldIds($this->definition(), $this->schema(), $this->schemaService);
    }

    /* ================================================================
     * Validator
     * ================================================================ */

    public function test_validator_accepts_the_reassignment_flow()
    {
        $this->assertSame([], $this->service->validateDefinition(
            $this->definition(), $this->validFieldIds(), $this->schemaService, $this->schema()
        ));
    }

    /**
     * A step assigned "whoever is chosen in a Person field" is only safe if that
     * field must be answered. An optional one can reach the step empty, leaving
     * it with nobody to go to.
     */
    public function test_validator_rejects_a_handler_from_an_optional_person_field()
    {
        $schema = $this->schema();
        foreach ($schema['elements'] as $i => $element) {
            if ($element['id'] === 'el_tech') {
                $schema['elements'][$i]['mandatory'] = false;
            }
        }

        $errors = $this->service->validateDefinition(
            $this->definition(), $this->validFieldIds(), $this->schemaService, $schema
        );

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Technician Name', $errors[0]);
        $this->assertStringContainsString('Set that field to Required in Form Design', $errors[0]);
    }

    public function test_validator_rejects_repeating_from_a_later_or_missing_step()
    {
        foreach (['nd_admin_missing', 'nd_br'] as $target) {
            $definition = $this->definition();
            $definition['nodes'][3]['branches'][0]['loop_to'] = $target;

            $errors = $this->service->validateDefinition(
                $definition, $this->validFieldIds(), $this->schemaService, $this->schema()
            );

            $this->assertNotEmpty(array_filter($errors, fn ($e) => str_contains($e, 'repeat')), "target: {$target}");
        }
    }

    public function test_validator_rejects_a_range_that_cannot_change_the_condition()
    {
        // Rewinding only to the technician section leaves nobody able to set Status.
        $definition = $this->definition();
        $definition['nodes'][3]['branches'][0]['loop_to'] = 'nd_tech';
        $definition['nodes'][2]['field_permissions'] = ['default' => 'read', 'overrides' => ['el_request' => 'edit']];

        $errors = $this->service->validateDefinition(
            $definition, $this->validFieldIds(), $this->schemaService, $this->schema()
        );

        $this->assertNotEmpty(array_filter($errors, fn ($e) => str_contains($e, 'cannot fill the field(s) its condition checks')));
    }

    public function test_validator_rejects_both_repeat_shapes_at_once()
    {
        $definition = $this->definition();
        $definition['nodes'][3]['branches'][0]['loop'] = true;

        $errors = $this->service->validateDefinition(
            $definition, $this->validFieldIds(), $this->schemaService, $this->schema()
        );

        $this->assertNotEmpty(array_filter($errors, fn ($e) => str_contains($e, 'cannot both repeat')));
    }

    public function test_validator_rejects_a_cleared_assignee_field_with_no_refill()
    {
        // Rewind starts AT the technician step: the round blanks el_tech and
        // nothing refills it, so the next round would have no handler.
        $definition = $this->definition();
        $definition['nodes'][3]['branches'][0]['loop_to'] = 'nd_tech';
        $definition['nodes'][1]['field_permissions'] = [
            'default' => 'read', 'overrides' => ['el_work' => 'edit', 'el_tech' => 'edit'],
        ];

        $errors = $this->service->validateDefinition(
            $definition, $this->validFieldIds(), $this->schemaService, $this->schema()
        );

        $this->assertNotEmpty(array_filter($errors, fn ($e) => str_contains($e, 'cleared each round')));
    }

    public function test_validator_requires_a_person_field_for_field_assigned_handlers()
    {
        $definition = $this->definition();
        $definition['nodes'][1]['assignee_field'] = 'el_status'; // a select, not a person

        $errors = $this->service->validateDefinition(
            $definition, $this->validFieldIds(), $this->schemaService, $this->schema()
        );

        $this->assertNotEmpty(array_filter($errors, fn ($e) => str_contains($e, 'must take its handler from a Person field')));

        $definition['nodes'][1]['assignee_field'] = null;
        $errors = $this->service->validateDefinition(
            $definition, $this->validFieldIds(), $this->schemaService, $this->schema()
        );
        $this->assertNotEmpty(array_filter($errors, fn ($e) => str_contains($e, 'needs a person field')));
    }

    /* ================================================================
     * Walk
     * ================================================================ */

    public function test_deferred_fields_cover_every_handler_owned_field()
    {
        $deferred = $this->deferred();
        sort($deferred);

        $this->assertSame(['el_status', 'el_tech', 'el_work'], $deferred);
    }

    public function test_reassignment_lifecycle()
    {
        $definition = $this->definition();
        $deferred   = $this->deferred();

        // 1. Submit: only the assignment step materialises; the technician step
        //    waits because nobody has been chosen yet.
        $r1 = $this->service->materializeFrom($definition, [], ['el_request' => 'Printer'], $deferred, true, []);
        $this->assertSame(['nd_assign'], array_column($r1['steps'], 'node_id'));
        $this->assertSame([], $r1['rounds']);
        $this->assertFalse($r1['complete']);

        // 2. Admin picks user 7 -> the technician step goes to user 7, and the
        //    following static step is revealed with it (pending, not current).
        $answers = ['el_request' => 'Printer', 'el_tech' => 7];
        $r2      = $this->service->materializeFrom($definition, $r1['cursor'], $answers, $deferred, true, []);
        $this->assertSame(['nd_tech', 'nd_admin'], array_column($r2['steps'], 'node_id'));
        $this->assertSame([7], $r2['steps'][0]['approver_ids']);
        $this->assertArrayNotHasKey('runtime_assigned', $r2['steps'][0]); // came from the form, not a pick

        // 3. Technician fills the work, but Admin Review is still pending, so
        //    the branch cannot be decided yet (reached = false).
        $answers['el_work'] = 'Replaced drum';
        $r3 = $this->service->materializeFrom($definition, $r2['cursor'], $answers, $deferred, false, []);
        $this->assertSame([], $r3['steps']);
        $this->assertSame([], $r3['trail']);

        // 4. Admin sets Status = Pending -> rewind to the assignment step as
        //    round 2. The range's steps are reported so the caller can archive
        //    and blank the answers they own.
        $answers['el_status'] = 'Pending';
        $r4 = $this->service->materializeFrom($definition, $r3['cursor'], $answers, $deferred, true, []);

        $this->assertSame(['nd_assign'], array_column($r4['steps'], 'node_id'));
        $this->assertSame(2, $r4['steps'][0]['iteration']);
        $this->assertCount(1, $r4['rounds']);
        $this->assertSame(2, $r4['rounds'][0]['iteration']);
        $this->assertSame(['nd_assign', 'nd_tech', 'nd_admin'], $r4['rounds'][0]['node_ids']);
        $this->assertSame('br_pending', $r4['trail'][0]['matched']);

        // 5. Round 2 starts from a blank slate (the caller cleared el_work and
        //    el_status): Admin appoints user 9 and the round repeats.
        $answers = ['el_request' => 'Printer', 'el_tech' => 9, 'el_work' => null, 'el_status' => null];
        $r5      = $this->service->materializeFrom($definition, $r4['cursor'], $answers, $deferred, true, []);
        $this->assertSame(['nd_tech', 'nd_admin'], array_column($r5['steps'], 'node_id'));
        $this->assertSame([9], $r5['steps'][0]['approver_ids']);
        $this->assertSame([2, 2], array_column($r5['steps'], 'iteration'));

        // 6. Round 2 finishes with Status = Complete -> the loop exits.
        $answers['el_work']   = 'Replaced roller';
        $answers['el_status'] = 'Complete';
        $r6 = $this->service->materializeFrom($definition, $r5['cursor'], $answers, $deferred, true, []);
        $this->assertSame([], $r6['steps']);
        $this->assertSame([], $r6['rounds']);
        $this->assertTrue($r6['complete']);
        $this->assertSame('br_done', $r6['trail'][0]['matched']);
    }

    public function test_field_assigned_handler_asks_for_a_pick_when_the_field_is_empty()
    {
        $definition = $this->definition();
        $deferred   = $this->deferred();

        $r1 = $this->service->materializeFrom($definition, [], [], $deferred, true, []);

        // The assignment step never filled el_tech: rather than dead-ending,
        // the walk asks the acting user to choose.
        $r2 = $this->service->materializeFrom($definition, $r1['cursor'], ['el_tech' => null], $deferred, true, []);
        $this->assertSame('assignee', $r2['needs']['type']);
        $this->assertSame('nd_tech', $r2['needs']['node_id']);

        // With a manual pick it proceeds, and the row is flagged as such.
        $r3 = $this->service->materializeFrom($definition, $r1['cursor'], ['el_tech' => null], $deferred, true, [4]);
        $this->assertSame([4], $r3['steps'][0]['approver_ids']);
        $this->assertTrue($r3['steps'][0]['runtime_assigned']);
    }

    public function test_field_assigned_handler_waits_until_reached()
    {
        $definition = $this->definition();
        $deferred   = $this->deferred();
        $r1 = $this->service->materializeFrom($definition, [], [], $deferred, true, []);

        // Assignment step still pending: nothing new may materialise even though
        // the person field already holds a value.
        $r2 = $this->service->materializeFrom($definition, $r1['cursor'], ['el_tech' => 7], $deferred, false, []);

        $this->assertSame([], $r2['steps']);
        $this->assertNull($r2['needs']);
        $this->assertSame($r1['cursor'], $r2['cursor']);
    }
}
