<?php

namespace Tests\Unit;

use App\Services\FormProcessService;
use App\Services\FormSchemaService;
use PHPUnit\Framework\TestCase;

class FormProcessResolveTest extends TestCase
{
    private FormProcessService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FormProcessService();
    }

    private function branchingDefinition(): array
    {
        return [
            'process_version' => 1,
            'nodes' => [
                ['id' => 'nd_mgr', 'type' => 'approval', 'name' => 'Manager Approval',
                 'approver_ids' => [5, 9], 'approval_mode' => 'any',
                 'field_permissions' => ['default' => 'read']],
                ['id' => 'nd_branch', 'type' => 'branch', 'branches' => [
                    ['id' => 'br_long', 'name' => 'Long leave',
                     'when' => ['logic' => 'or', 'groups' => [['logic' => 'and', 'conditions' => [
                         ['field' => 'el_days', 'operator' => 'gt', 'value' => '3'],
                     ]]]],
                     'nodes' => [
                         ['id' => 'nd_hr', 'type' => 'approval', 'name' => 'HR Approval',
                          'approver_ids' => [12, 13], 'approval_mode' => 'all',
                          'field_permissions' => ['default' => 'read']],
                     ]],
                    ['id' => 'br_short', 'name' => 'Short leave',
                     'when' => ['logic' => 'or', 'groups' => [['logic' => 'and', 'conditions' => [
                         ['field' => 'el_days', 'operator' => 'gt', 'value' => '1'],
                     ]]]],
                     'nodes' => [
                         ['id' => 'nd_lead', 'type' => 'approval', 'name' => 'Team Lead Approval',
                          'approver_ids' => [20], 'approval_mode' => 'any',
                          'field_permissions' => ['default' => 'read']],
                     ]],
                    ['id' => 'br_default', 'name' => 'Default', 'when' => null, 'nodes' => []],
                ]],
                ['id' => 'nd_cc', 'type' => 'cc', 'name' => 'Notify Finance', 'user_ids' => [30]],
            ],
        ];
    }

    public function test_resolve_takes_first_matching_branch()
    {
        // el_days = 5 matches BOTH branch conditions; the first must win.
        $resolved = $this->service->resolve($this->branchingDefinition(), ['el_days' => '5']);

        $this->assertSame(['nd_mgr', 'nd_hr', 'nd_cc'], array_column($resolved['chain'], 'node_id'));
        $this->assertSame('br_long', $resolved['chain'][1]['source_branch_id']);
        $this->assertSame([['branch_node' => 'nd_branch', 'matched' => 'br_long', 'matched_name' => 'Long leave']],
            $resolved['branch_trail']);
    }

    public function test_resolve_falls_through_to_later_branch()
    {
        $resolved = $this->service->resolve($this->branchingDefinition(), ['el_days' => '2']);

        $this->assertSame(['nd_mgr', 'nd_lead', 'nd_cc'], array_column($resolved['chain'], 'node_id'));
        $this->assertSame('br_short', $resolved['branch_trail'][0]['matched']);
    }

    public function test_resolve_uses_default_branch_when_nothing_matches()
    {
        // el_days = 1 matches neither condition -> default (empty) branch,
        // and the chain rejoins at the CC node after the branch block.
        $resolved = $this->service->resolve($this->branchingDefinition(), ['el_days' => '1']);

        $this->assertSame(['nd_mgr', 'nd_cc'], array_column($resolved['chain'], 'node_id'));
        $this->assertSame('br_default', $resolved['branch_trail'][0]['matched']);
    }

    public function test_resolve_handles_empty_definition()
    {
        $resolved = $this->service->resolve(['process_version' => 1, 'nodes' => []], []);
        $this->assertSame([], $resolved['chain']);
        $this->assertSame([], $resolved['branch_trail']);
    }

    public function test_resolve_supports_nested_branches_even_though_ui_caps_depth()
    {
        $definition = [
            'process_version' => 1,
            'nodes' => [
                ['id' => 'nd_b1', 'type' => 'branch', 'branches' => [
                    ['id' => 'br_a', 'name' => 'A',
                     'when' => ['logic' => 'or', 'groups' => [['logic' => 'and', 'conditions' => [
                         ['field' => 'x', 'operator' => 'equals', 'value' => '1'],
                     ]]]],
                     'nodes' => [
                         ['id' => 'nd_b2', 'type' => 'branch', 'branches' => [
                             ['id' => 'br_inner', 'name' => 'Inner',
                              'when' => ['logic' => 'or', 'groups' => [['logic' => 'and', 'conditions' => [
                                  ['field' => 'y', 'operator' => 'equals', 'value' => '2'],
                              ]]]],
                              'nodes' => [
                                  ['id' => 'nd_deep', 'type' => 'approval', 'name' => 'Deep',
                                   'approver_ids' => [1], 'approval_mode' => 'any'],
                              ]],
                             ['id' => 'br_inner_default', 'name' => 'Else', 'when' => null, 'nodes' => []],
                         ]],
                     ]],
                    ['id' => 'br_else', 'name' => 'Else', 'when' => null, 'nodes' => []],
                ]],
            ],
        ];

        $resolved = $this->service->resolve($definition, ['x' => '1', 'y' => '2']);
        $this->assertSame(['nd_deep'], array_column($resolved['chain'], 'node_id'));
        $this->assertCount(2, $resolved['branch_trail']);
    }

    public function test_validate_accepts_a_correct_definition()
    {
        $errors = $this->service->validateDefinition(
            $this->branchingDefinition(),
            ['el_days' => true],
            new FormSchemaService()
        );

        $this->assertSame([], $errors);
    }

    public function test_validate_rejects_structural_problems()
    {
        $schemaService = new FormSchemaService();
        $fieldIds = ['el_days' => true];

        // Approval node without approvers.
        $bad = $this->branchingDefinition();
        $bad['nodes'][0]['approver_ids'] = [];
        $this->assertNotEmpty($this->service->validateDefinition($bad, $fieldIds, $schemaService));

        // Non-last branch without a condition.
        $bad = $this->branchingDefinition();
        $bad['nodes'][1]['branches'][0]['when'] = null;
        $this->assertNotEmpty($this->service->validateDefinition($bad, $fieldIds, $schemaService));

        // Last branch with a condition (no default).
        $bad = $this->branchingDefinition();
        $bad['nodes'][1]['branches'][2]['when'] = ['logic' => 'or', 'groups' => [['logic' => 'and', 'conditions' => [
            ['field' => 'el_days', 'operator' => 'equals', 'value' => '1'],
        ]]]];
        $this->assertNotEmpty($this->service->validateDefinition($bad, $fieldIds, $schemaService));

        // Branch condition referencing an unknown field.
        $bad = $this->branchingDefinition();
        $bad['nodes'][1]['branches'][0]['when']['groups'][0]['conditions'][0]['field'] = 'el_missing';
        $this->assertNotEmpty($this->service->validateDefinition($bad, $fieldIds, $schemaService));

        // Nested branch exceeds the v1 depth cap.
        $bad = $this->branchingDefinition();
        $bad['nodes'][1]['branches'][0]['nodes'][] = ['id' => 'nd_nested', 'type' => 'branch', 'branches' => [
            ['id' => 'br_x', 'name' => 'X', 'when' => ['logic' => 'or', 'groups' => [['logic' => 'and', 'conditions' => [
                ['field' => 'el_days', 'operator' => 'equals', 'value' => '1'],
            ]]]], 'nodes' => []],
            ['id' => 'br_y', 'name' => 'Else', 'when' => null, 'nodes' => []],
        ]];
        $errors = $this->service->validateDefinition($bad, $fieldIds, $schemaService);
        $this->assertContains('Branches cannot be nested inside branches.', $errors);

        // Duplicate node ids.
        $bad = $this->branchingDefinition();
        $bad['nodes'][2]['id'] = 'nd_mgr';
        $this->assertNotEmpty($this->service->validateDefinition($bad, $fieldIds, $schemaService));
    }

    public function test_resolve_materialises_fill_nodes_with_assignees_as_approver_ids()
    {
        $definition = [
            'process_version' => 1,
            'nodes' => [
                ['id' => 'nd_fill', 'type' => 'fill', 'name' => 'Technician Section',
                 'assignee_ids' => [7, 8],
                 'field_permissions' => ['default' => 'read', 'overrides' => ['grp_tech' => 'edit']]],
                ['id' => 'nd_mgr', 'type' => 'approval', 'name' => 'Manager Approval',
                 'approver_ids' => [5], 'approval_mode' => 'any'],
            ],
        ];

        $resolved = $this->service->resolve($definition, []);

        $this->assertSame(['nd_fill', 'nd_mgr'], array_column($resolved['chain'], 'node_id'));
        $this->assertSame('fill', $resolved['chain'][0]['type']);
        $this->assertSame([7, 8], $resolved['chain'][0]['approver_ids']);
    }

    public function test_validate_enforces_fill_node_rules()
    {
        $schemaService = new FormSchemaService();
        $fieldIds = ['el_days' => true];

        $good = ['process_version' => 1, 'nodes' => [
            ['id' => 'nd_fill', 'type' => 'fill', 'name' => 'Tech Section',
             'assignee_ids' => [7],
             'field_permissions' => ['default' => 'read', 'overrides' => ['el_days' => 'edit']]],
        ]];
        $this->assertSame([], $this->service->validateDefinition($good, $fieldIds, $schemaService));

        // No assignees.
        $bad = $good;
        $bad['nodes'][0]['assignee_ids'] = [];
        $this->assertNotEmpty($this->service->validateDefinition($bad, $fieldIds, $schemaService));

        // No field marked "Fill".
        $bad = $good;
        $bad['nodes'][0]['field_permissions']['overrides'] = ['el_days' => 'read'];
        $this->assertNotEmpty($this->service->validateDefinition($bad, $fieldIds, $schemaService));
    }

    public function test_deferred_field_ids_collects_fill_edit_targets_across_branches()
    {
        $schemaService = new FormSchemaService();
        $schema = [
            'schema_version' => 2,
            'groups' => [['id' => 'grp_tech', 'label' => 'Technician', 'order' => 10, 'visible_when' => null]],
            'elements' => [
                ['id' => 'el_a', 'kind' => 'field', 'type' => 'text', 'label' => 'A', 'group_id' => null, 'order' => 10],
                ['id' => 'el_b', 'kind' => 'field', 'type' => 'text', 'label' => 'B', 'group_id' => 'grp_tech', 'order' => 10],
                ['id' => 'el_c', 'kind' => 'field', 'type' => 'text', 'label' => 'C', 'group_id' => 'grp_tech', 'order' => 20],
                ['id' => 'el_d', 'kind' => 'field', 'type' => 'text', 'label' => 'D', 'group_id' => null, 'order' => 20],
                // A description sharing the handler-edited group must never be deferred —
                // it's static text, not something a handler fills (regression for a bug
                // where it silently vanished from the submitter's fill page).
                ['id' => 'el_desc', 'kind' => 'description', 'text' => 'Instructions', 'group_id' => 'grp_tech', 'order' => 30],
            ],
        ];

        $definition = ['process_version' => 1, 'nodes' => [
            // Group override expands to both members.
            ['id' => 'nd_f1', 'type' => 'fill', 'name' => 'Tech', 'assignee_ids' => [7],
             'field_permissions' => ['default' => 'read', 'overrides' => ['grp_tech' => 'edit']]],
            // Fill node inside a branch also defers its fields.
            ['id' => 'nd_br', 'type' => 'branch', 'branches' => [
                ['id' => 'br_1', 'name' => 'X',
                 'when' => ['logic' => 'or', 'groups' => [['logic' => 'and', 'conditions' => [
                     ['field' => 'el_a', 'operator' => 'is_not_empty', 'value' => null]]]]],
                 'nodes' => [
                     ['id' => 'nd_f2', 'type' => 'fill', 'name' => 'Extra', 'assignee_ids' => [9],
                      'field_permissions' => ['default' => 'read', 'overrides' => ['el_d' => 'edit']]],
                 ]],
                ['id' => 'br_2', 'name' => 'Else', 'when' => null, 'nodes' => []],
            ]],
        ]];

        $deferred = $this->service->deferredFieldIds($definition, $schema, $schemaService);

        sort($deferred);
        $this->assertSame(['el_b', 'el_c', 'el_d'], $deferred);
    }

    public function test_summary_flattens_top_level_with_branch_details()
    {
        $summary = $this->service->summary($this->branchingDefinition());

        $this->assertCount(3, $summary);
        $this->assertSame('approval', $summary[0]['type']);
        $this->assertSame('branch', $summary[1]['type']);
        $this->assertCount(3, $summary[1]['branches']);
        $this->assertSame('HR Approval', $summary[1]['branches'][0]['steps'][0]['name']);
        $this->assertSame('cc', $summary[2]['type']);
    }
}
