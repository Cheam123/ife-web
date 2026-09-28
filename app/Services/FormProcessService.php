<?php

namespace App\Services;

/**
 * Owns the versioned process_definition JSON tree stored on forms.
 *
 * {
 *   "process_version": 1,
 *   "nodes": [
 *     {"id":"nd_f","type":"fill","name":"Technician Section",
 *      "assignee_ids":[7],"field_permissions":{"default":"read","overrides":{"grp_tech":"edit"}}},
 *     {"id":"nd_a","type":"approval","name":"Manager Approval",
 *      "approver_ids":[5,9],"approval_mode":"any",
 *      "field_permissions":{"default":"read","overrides":{"el_x":"edit","grp_y":"hidden"}}},
 *     {"id":"nd_b","type":"cc","name":"Notify Finance","user_ids":[20]},
 *     {"id":"nd_c","type":"branch","branches":[
 *        {"id":"br_1","name":"Long leave","when":{...condition schema...},"nodes":[...]},
 *        {"id":"br_2","name":"Default","when":null,"nodes":[]}
 *     ]}
 *   ]
 * }
 *
 * A "fill" node is a filling phase: the flow pauses there until one of its
 * assignees completes the fields marked "edit" in its field_permissions.
 * Fields owned by fill nodes are deferred — the original submitter neither
 * sees nor must complete them (see deferredFieldIds()).
 *
 * Branches are evaluated in order, first match wins; the trailing branch with
 * when=null is the mandatory default. The engine is recursive, but v1 caps
 * branch nesting at depth 1 (enforced here and mirrored in the builder UI),
 * so lifting the cap later needs no schema change.
 *
 * Loops & runtime evaluation:
 * - A conditional branch arm may carry "loop": true — after its nodes all
 *   complete, the branch re-evaluates (first match wins over all arms); a
 *   conditional arm matching starts another round, the default arm matching
 *   exits the loop.
 * - A fill node may carry "assignee_mode": "runtime" — no design-time
 *   assignees; the user whose action activates the step picks who handles it.
 * - Any branch that loops, or whose conditions reference a deferred field,
 *   is a RUNTIME branch: it is evaluated when the flow reaches it (via
 *   materializeFrom()), not at submit time.
 */
class FormProcessService
{
    public const MAX_BRANCH_DEPTH = 1;

    /** Per-walk safety cap on evaluations of a single branch node. */
    public const MAX_BRANCH_EVALS = 25;

    private FormConditionEvaluator $conditions;

    public function __construct(?FormConditionEvaluator $conditions = null)
    {
        $this->conditions = $conditions ?? new FormConditionEvaluator();
    }

    public function normalize($raw): array
    {
        $raw = $raw ?: [];

        return [
            'process_version' => (int) ($raw['process_version'] ?? 1),
            'nodes'           => array_values($raw['nodes'] ?? []),
        ];
    }

    /**
     * @param array      $validFieldIds element id => true (from the form schema, for branch conditions)
     * @param array|null $schema        full normalized form schema — needed to expand group
     *                                  permission overrides for the loop-editability rule
     * @return array list of error strings (empty = valid)
     */
    public function validateDefinition(array $definition, array $validFieldIds, FormSchemaService $schemaService, ?array $schema = null): array
    {
        $errors  = [];
        $seenIds = [];

        $this->validateNodes($definition['nodes'] ?? [], 0, $validFieldIds, $schemaService, $errors, $seenIds, $schema);

        if ($schema !== null) {
            foreach ($this->unreachableConditionErrors($definition, $schema, $schemaService) as $error) {
                $errors[] = $error;
            }
        }

        return $errors;
    }

    /**
     * A field whose condition can never come true for whoever is looking at it.
     *
     * If "show B when A is X" is set up but A is answered in a later Handler
     * step, the submitter never sees A, so B stays hidden on their form. B then
     * has to be editable in that same step (or a later one) or it is stranded:
     * hidden at submit time, and not on the form of the person who answers A.
     *
     * The builder used to accept this quietly and the field simply never
     * appeared, with nothing to explain why.
     *
     * @return array<string> error strings
     */
    private function unreachableConditionErrors(array $definition, array $schema, FormSchemaService $schemaService): array
    {
        $elements = collect($schema['elements'] ?? [])->keyBy('id');
        $groups   = collect($schema['groups'] ?? [])->keyBy('id');

        // Which fill step (by position) first lets each field be edited.
        $stageOf = [];
        $position = 0;
        foreach ($this->allNodes($definition) as $node) {
            if (($node['type'] ?? null) !== 'fill') {
                continue;
            }
            $position++;
            foreach ($schemaService->resolveFieldPermissions($schema, $node['field_permissions'] ?: []) as $fieldId => $level) {
                if ($level === 'edit' && !isset($stageOf[$fieldId])) {
                    $stageOf[$fieldId] = $position;
                }
            }
        }

        $errors = [];

        foreach ($elements as $element) {
            // A field's own condition, or the one on the section holding it.
            $conditions = [$element['visible_when'] ?? null];
            if (!empty($element['group_id']) && $groups->has($element['group_id'])) {
                $conditions[] = $groups->get($element['group_id'])['visible_when'] ?? null;
            }

            foreach ($conditions as $condition) {
                foreach ($this->conditionFieldIds($condition) as $triggerId) {
                    if (!isset($stageOf[$triggerId])) {
                        continue; // the submitter answers it — always reachable
                    }

                    $shownAt   = $stageOf[$element['id']] ?? null;
                    $answeredAt = $stageOf[$triggerId];

                    if ($shownAt === null || $shownAt < $answeredAt) {
                        $errors[] = sprintf(
                            '"%s" is shown only when "%s" has a certain answer, but "%s" is filled in at a later step. '
                            . 'Add "%s" to that step (or a later one), or the field can never appear.',
                            $element['label'] ?? $element['id'],
                            $elements->get($triggerId)['label'] ?? $triggerId,
                            $elements->get($triggerId)['label'] ?? $triggerId,
                            $element['label'] ?? $element['id']
                        );
                    }
                }
            }
        }

        return array_values(array_unique($errors));
    }

    private function validateNodes(array $nodes, int $depth, array $validFieldIds,
                                   FormSchemaService $schemaService, array &$errors, array &$seenIds,
                                   ?array $schema = null): void
    {
        foreach ($nodes as $node) {
            $id = $node['id'] ?? null;
            if (!$id) {
                $errors[] = 'A process node is missing its id.';
            } elseif (isset($seenIds[$id])) {
                $errors[] = "Duplicate process node id: {$id}.";
            } else {
                $seenIds[$id] = true;
            }

            $type = $node['type'] ?? null;
            $name = trim((string) ($node['name'] ?? ''));

            switch ($type) {
                case 'approval':
                    if ($name === '') {
                        $errors[] = 'Every approval step needs a name.';
                    }
                    $approvers = array_filter(array_map('intval', $node['approver_ids'] ?? []));
                    if (empty($approvers)) {
                        $errors[] = 'Approval step "' . ($name ?: $id) . '" needs at least one approver.';
                    }
                    if (!in_array($node['approval_mode'] ?? 'any', ['any', 'all'], true)) {
                        $errors[] = 'Approval step "' . ($name ?: $id) . '" has an invalid approval mode.';
                    }
                    break;

                case 'fill':
                    if ($name === '') {
                        $errors[] = 'Every handler step needs a name.';
                    }
                    $assigneeMode = $node['assignee_mode'] ?? 'fixed';
                    if (!in_array($assigneeMode, ['fixed', 'runtime', 'field'], true)) {
                        $errors[] = 'Handler step "' . ($name ?: $id) . '" has an invalid assignee mode.';
                    }
                    if ($assigneeMode === 'fixed'
                        && empty(array_filter(array_map('intval', $node['assignee_ids'] ?? [])))) {
                        $errors[] = 'Handler step "' . ($name ?: $id) . '" needs at least one handler.';
                    }
                    if ($assigneeMode === 'field') {
                        $assigneeField = $node['assignee_field'] ?? null;
                        $element = $assigneeField && $schema !== null
                            ? collect($schema['elements'] ?? [])->firstWhere('id', $assigneeField)
                            : null;

                        if (!$assigneeField) {
                            $errors[] = 'Handler step "' . ($name ?: $id) . '" needs a person field to take its handler from.';
                        } elseif ($schema !== null && (!$element || ($element['type'] ?? null) !== 'user')) {
                            $errors[] = 'Handler step "' . ($name ?: $id) . '" must take its handler from a Person field.';
                        } elseif ($schema !== null && empty($element['mandatory'])) {
                            // A person field that chooses a handler must always be
                            // answered, or the step has nobody to go to.
                            //
                            // Worded as an instruction. "X is not required" reads
                            // as a claim about the person, and "X can be left
                            // blank" reads as permission to leave it blank —
                            // neither is meant.
                            $errors[] = 'Handler step "' . ($name ?: $id) . '" takes its handler from "'
                                . ($element['label'] ?? $assigneeField)
                                . '". Set that field to Required in Form Design so it must always be answered.';
                        }
                    }
                    $editTargets = array_filter(
                        $node['field_permissions']['overrides'] ?? [],
                        fn ($level) => $level === 'edit'
                    );
                    if (empty($editTargets)) {
                        $errors[] = 'Handler step "' . ($name ?: $id) . '" needs at least one field marked as "Fill" in Form Permissions.';
                    }
                    break;

                case 'cc':
                    if (empty(array_filter(array_map('intval', $node['user_ids'] ?? [])))) {
                        $errors[] = 'CC step "' . ($name ?: $id) . '" needs at least one recipient.';
                    }
                    break;

                case 'branch':
                    if ($depth >= self::MAX_BRANCH_DEPTH) {
                        $errors[] = 'Branches cannot be nested inside branches.';
                        break;
                    }
                    $branches = array_values($node['branches'] ?? []);
                    if (count($branches) < 2) {
                        $errors[] = 'A branch block needs at least two branches.';
                        break;
                    }
                    foreach ($branches as $i => $branch) {
                        $isLast    = $i === count($branches) - 1;
                        $condition = $branch['when'] ?? null;

                        if ($isLast && !empty($condition)) {
                            $errors[] = 'The last branch of a branch block must be the default (no condition).';
                        }
                        if (!$isLast && empty($condition)) {
                            $errors[] = 'Only the last branch of a branch block may have no condition.';
                        }
                        if (!empty($condition)) {
                            $errors = array_merge($errors, $schemaService->validateConditionSchema(
                                $condition,
                                $validFieldIds,
                                'branch "' . ($branch['name'] ?? '?') . '"'
                            ));
                        }

                        $loopTo  = $branch['loop_to'] ?? null;
                        $armName = $branch['name'] ?? '?';

                        if (!empty($branch['loop']) && $loopTo) {
                            $errors[] = 'Branch "' . $armName . '" cannot both repeat its own path and repeat from an earlier step.';
                        } elseif ($loopTo) {
                            // "Repeat from an earlier step": the repeated range is
                            // [target .. this branch) in the branch's own list.
                            $targetIndex = $this->indexOfNode($nodes, (string) $loopTo);
                            $branchIndex = $this->indexOfNode($nodes, (string) $id);

                            if ($isLast) {
                                $errors[] = 'The default branch cannot repeat — only conditional branches can loop.';
                            } elseif ($targetIndex === null) {
                                $errors[] = 'Branch "' . $armName . '" repeats from a step that is not in the same flow.';
                            } elseif ($branchIndex === null || $targetIndex >= $branchIndex) {
                                $errors[] = 'Branch "' . $armName . '" must repeat from a step that comes before it.';
                            } else {
                                $range = array_slice($nodes, $targetIndex, $branchIndex - $targetIndex);
                                if (!$this->containsBlockingNode($range)) {
                                    $errors[] = 'Branch "' . $armName . '" repeats a range with no Handler or Approval step.';
                                } elseif ($schema !== null && !$this->rangeCanChangeCondition($range, $condition, $schema, $schemaService)) {
                                    $errors[] = 'Branch "' . $armName . '" repeats steps that cannot fill the field(s) its condition checks — otherwise the loop can never end.';
                                } elseif ($schema !== null) {
                                    $errors = array_merge($errors, $this->rangeAssigneeFieldErrors($range, $armName, $schema, $schemaService));
                                }
                            }
                        } elseif (!empty($branch['loop'])) {
                            if ($isLast) {
                                $errors[] = 'The default branch cannot repeat — only conditional branches can loop.';
                            } elseif (!$this->containsBlockingNode($branch['nodes'] ?? [])) {
                                $errors[] = 'Loop branch "' . $armName . '" needs at least one Handler or Approval step inside it.';
                            } elseif ($schema !== null && !$this->rangeCanChangeCondition($branch['nodes'] ?? [], $condition, $schema, $schemaService)) {
                                $errors[] = 'Loop branch "' . $armName . '" needs a Handler step that can fill the field(s) its condition checks — otherwise the loop can never end.';
                            }
                        }

                        $this->validateNodes($branch['nodes'] ?? [], $depth + 1, $validFieldIds, $schemaService, $errors, $seenIds, $schema);
                    }
                    break;

                default:
                    $errors[] = 'A process node has an unknown type.';
            }
        }
    }

    /**
     * Walks the tree against the submitted answers and returns the resolved
     * flat chain plus the branch decisions taken.
     *
     * @return array{chain: array, branch_trail: array}
     *   chain entries: {node_id, type, name, approver_ids|user_ids,
     *                   approval_mode, field_permissions, source_branch_id}
     *   branch_trail entries: {branch_node, matched, matched_name}
     */
    public function resolve(array $definition, array $answersById): array
    {
        $chain = [];
        $trail = [];

        $this->resolveNodes($definition['nodes'] ?? [], $answersById, null, $chain, $trail);

        return ['chain' => $chain, 'branch_trail' => $trail];
    }

    private function resolveNodes(array $nodes, array $answersById, ?string $sourceBranchId,
                                  array &$chain, array &$trail): void
    {
        foreach ($nodes as $node) {
            $type = $node['type'] ?? null;

            if ($type === 'branch') {
                $branches = array_values($node['branches'] ?? []);
                $matched  = null;

                foreach ($branches as $i => $branch) {
                    $isLast = $i === count($branches) - 1;
                    $when   = $branch['when'] ?? null;

                    if ((empty($when) && $isLast) || (!empty($when) && $this->conditions->evaluate($when, $answersById))) {
                        $matched = $branch;
                        break;
                    }
                }

                $trail[] = [
                    'branch_node'  => $node['id'] ?? null,
                    'matched'      => $matched['id'] ?? null,
                    'matched_name' => $matched['name'] ?? null,
                ];

                if ($matched) {
                    $this->resolveNodes($matched['nodes'] ?? [], $answersById, $matched['id'] ?? null, $chain, $trail);
                }

                continue;
            }

            if ($type === 'approval') {
                $chain[] = [
                    'node_id'           => $node['id'] ?? null,
                    'type'              => 'approval',
                    'name'              => $node['name'] ?? 'Approval',
                    'approver_ids'      => array_values(array_filter(array_map('intval', $node['approver_ids'] ?? []))),
                    'approval_mode'     => in_array($node['approval_mode'] ?? 'any', ['any', 'all'], true)
                                           ? ($node['approval_mode'] ?? 'any') : 'any',
                    'field_permissions' => $node['field_permissions'] ?? ['default' => 'read'],
                    'source_branch_id'  => $sourceBranchId,
                ];
            } elseif ($type === 'fill') {
                $chain[] = [
                    'node_id'           => $node['id'] ?? null,
                    'type'              => 'fill',
                    'name'              => $node['name'] ?? 'Fill Section',
                    // Materialised into approver_ids so the runtime row/query
                    // shape stays uniform with approval stages.
                    'approver_ids'      => array_values(array_filter(array_map('intval', $node['assignee_ids'] ?? []))),
                    'approval_mode'     => 'any',
                    'field_permissions' => $node['field_permissions'] ?? ['default' => 'read'],
                    'source_branch_id'  => $sourceBranchId,
                ];
            } elseif ($type === 'cc') {
                $chain[] = [
                    'node_id'           => $node['id'] ?? null,
                    'type'              => 'cc',
                    'name'              => $node['name'] ?? 'Notify',
                    'user_ids'          => array_values(array_filter(array_map('intval', $node['user_ids'] ?? []))),
                    'field_permissions' => $node['field_permissions'] ?? ['default' => 'read'],
                    'source_branch_id'  => $sourceBranchId,
                ];
            }
        }
    }

    /**
     * Element ids owned by later fill phases: every FIELD (or field member
     * of a group) marked "edit" on ANY fill node anywhere in the tree. The
     * original submitter neither sees nor must complete these fields —
     * branch outcomes aren't known at submit time, so fields from every
     * branch's fill nodes are deferred.
     *
     * Description blocks are never deferred even when they share a group
     * with handler-edited fields — there is nothing for a handler to
     * "fill" on a description, it's static text meant for everyone who
     * can see that section.
     *
     * @return array<string> element ids
     */
    public function deferredFieldIds(array $definition, array $schema, FormSchemaService $schemaService): array
    {
        $ids = [];

        $fieldIds = collect($schema['elements'] ?? [])
            ->filter(fn ($e) => ($e['kind'] ?? 'field') === 'field')
            ->pluck('id')
            ->flip();

        $walk = function ($nodes) use (&$walk, &$ids, $schema, $schemaService, $fieldIds) {
            foreach ($nodes as $node) {
                $type = $node['type'] ?? null;
                if ($type === 'branch') {
                    foreach ($node['branches'] ?? [] as $branch) {
                        $walk($branch['nodes'] ?? []);
                    }
                    continue;
                }
                if ($type !== 'fill') {
                    continue;
                }
                // Expand group overrides to member element ids.
                $permissions = $node['field_permissions'] ?? [];
                $permissions['default'] = 'read'; // only explicit "edit" overrides defer fields
                $resolved = $schemaService->resolveFieldPermissions($schema, $permissions);
                foreach ($resolved as $elementId => $level) {
                    if ($level === 'edit' && $fieldIds->has($elementId)) {
                        $ids[] = $elementId;
                    }
                }
            }
        };

        $walk($definition['nodes'] ?? []);

        return array_values(array_unique($ids));
    }

    /**
     * Incrementally materialises process steps from a saved cursor — the
     * runtime counterpart of resolve() that supports late-bound branch
     * conditions, loops, and runtime-assigned handlers.
     *
     * Cursor = stack of frames (root first, at most one branch frame while
     * MAX_BRANCH_DEPTH is 1):
     *   {"scope":"root","index":int}
     *   {"scope":"branch","branch_node_id":str,"arm_id":str,"index":int,"iteration":int}
     *
     * Static nodes and static branches materialise eagerly so future stages
     * stay visible in timelines. The walk pauses at:
     *   - a runtime branch until it is REACHED ($reached: no pending blocking
     *     step exists before it — degrades to false once this walk emits a
     *     blocking step);
     *   - a handler whose assignee is decided at runtime (assignee_mode
     *     'runtime', or 'field' whose person field is still empty) until it
     *     is reached AND $nextAssigneeIds was supplied (otherwise returns
     *     needs = {type:'assignee', ...});
     *   - the end of a loop arm until reached, then re-evaluates the branch
     *     (conditional arm match = next round, default arm match = exit).
     *
     * Two repeat shapes: `loop` re-runs the arm's own steps; `loop_to`
     * rewinds the branch's PARENT list to an earlier step so a whole earlier
     * phase runs again (e.g. "reassign the technician").
     *
     * Pure array-in/array-out: no models, no side effects.
     *
     * @return array{steps: array, trail: array, cursor: array, complete: bool, needs: ?array, rounds: array}
     */
    public function materializeFrom(array $definition, array $cursor, array $answersById,
                                    array $deferredIds, bool $reached, array $nextAssigneeIds = []): array
    {
        $steps    = [];
        $trail    = [];
        $rounds   = [];
        $needs    = null;
        $complete = false;

        $frames = array_values($cursor ?: []);
        if (empty($frames)) {
            $frames = [['scope' => 'root', 'index' => 0, 'iteration' => 1]];
        }

        $evalCounts      = [];
        $deferredFlip    = array_flip($deferredIds);
        $nextAssigneeIds = array_values(array_unique(array_filter(array_map('intval', $nextAssigneeIds))));

        while (true) {
            $ti    = count($frames) - 1;
            $frame = $frames[$ti];
            $nodes = $this->frameNodes($definition, $frame);
            $index = (int) ($frame['index'] ?? 0);

            if ($index >= count($nodes)) {
                if (($frame['scope'] ?? 'root') === 'branch') {
                    $branchNode = $this->findBranchNode($definition['nodes'] ?? [], (string) ($frame['branch_node_id'] ?? ''));
                    $arm        = $branchNode ? $this->findArm($branchNode, (string) ($frame['arm_id'] ?? '')) : null;
                    $branchId   = (string) ($branchNode['id'] ?? '');

                    // "Repeat from an earlier step": rewind the parent list.
                    if ($branchNode && $arm && !empty($arm['loop_to']) && !empty($arm['when'])) {
                        if (!$reached) {
                            break; // the arm's own steps are still pending
                        }
                        array_pop($frames);
                        $pi          = count($frames) - 1;
                        $parentNodes = $this->frameNodes($definition, $frames[$pi]);
                        $targetIndex = $this->indexOfNode($parentNodes, (string) $arm['loop_to']);
                        $branchIndex = $this->indexOfNode($parentNodes, $branchId);

                        if ($targetIndex === null || $branchIndex === null || $targetIndex >= $branchIndex) {
                            continue; // unresolvable target (validator prevents this): fall through
                        }

                        $round                    = (int) ($frames[$pi]['iteration'] ?? 1) + 1;
                        $frames[$pi]['index']     = $targetIndex;
                        $frames[$pi]['iteration'] = $round;
                        $rounds[]                 = [
                            'iteration'   => $round,
                            'branch_node' => $branchId,
                            'node_ids'    => $this->collectNodeIds(array_slice($parentNodes, $targetIndex, $branchIndex - $targetIndex)),
                        ];
                        continue;
                    }

                    // "Repeat this path": re-evaluate the branch itself.
                    if ($branchNode && $arm && !empty($arm['loop']) && !empty($arm['when'])) {
                        if (!$reached) {
                            break; // this round's blocking steps are still pending
                        }
                        $evalCounts[$branchId] = ($evalCounts[$branchId] ?? 0) + 1;
                        $forced   = $evalCounts[$branchId] > self::MAX_BRANCH_EVALS;
                        $matched  = $this->matchBranchArm($branchNode, $answersById, $forced);
                        $round    = (int) ($frame['iteration'] ?? 1) + 1;
                        $trail[]  = $this->trailEntry($branchId, $matched, $round, $forced);

                        if ($matched) {
                            // Default arm exits the loop — its steps are not a "round".
                            $isRound     = !empty($matched['when']);
                            $frames[$ti] = [
                                'scope'          => 'branch',
                                'branch_node_id' => $branchId,
                                'arm_id'         => (string) ($matched['id'] ?? ''),
                                'index'          => 0,
                                'iteration'      => $isRound ? $round : 1,
                            ];
                            if ($isRound) {
                                $rounds[] = [
                                    'iteration'   => $round,
                                    'branch_node' => $branchId,
                                    'node_ids'    => $this->collectNodeIds($matched['nodes'] ?? []),
                                ];
                            }
                            continue;
                        }
                    }

                    array_pop($frames); // parent index already points past the branch
                    continue;
                }

                $complete = true;
                break;
            }

            $node      = $nodes[$index];
            $type      = $node['type'] ?? null;
            $iteration = (int) ($frame['iteration'] ?? 1);

            if ($type === 'branch') {
                if (!$reached && $this->isRuntimeBranch($node, $deferredFlip)) {
                    break; // evaluated only once the flow actually arrives here
                }
                $branchId = (string) ($node['id'] ?? '');
                $evalCounts[$branchId] = ($evalCounts[$branchId] ?? 0) + 1;
                $forced   = $evalCounts[$branchId] > self::MAX_BRANCH_EVALS;
                $matched  = $this->matchBranchArm($node, $answersById, $forced);
                $trail[]  = $this->trailEntry($branchId, $matched, $iteration, $forced);

                $frames[$ti]['index'] = $index + 1;
                if ($matched) {
                    $frames[] = [
                        'scope'          => 'branch',
                        'branch_node_id' => $branchId,
                        'arm_id'         => (string) ($matched['id'] ?? ''),
                        'index'          => 0,
                        'iteration'      => $iteration,
                    ];
                }
                continue;
            }

            $inBranch = ($frame['scope'] ?? 'root') === 'branch';
            $armId    = $inBranch ? ($frame['arm_id'] ?? null) : null;

            if ($type === 'fill' && in_array($node['assignee_mode'] ?? 'fixed', ['runtime', 'field'], true)) {
                if (!$reached) {
                    break; // cannot pre-materialise: the handler isn't known yet
                }

                // A person field wins; a manual pick covers the empty-field case.
                $fromField = ($node['assignee_mode'] === 'field')
                    ? $this->assigneesFromField($node, $answersById)
                    : [];
                $assignees = $fromField ?: $nextAssigneeIds;

                if (empty($assignees)) {
                    $needs = [
                        'type'    => 'assignee',
                        'node_id' => $node['id'] ?? null,
                        'name'    => $node['name'] ?? 'Handler',
                    ];
                    break;
                }

                $step                 = $this->makeStep($node, $armId, $iteration);
                $step['approver_ids'] = $assignees;
                if (empty($fromField)) {
                    $step['runtime_assigned'] = true;
                    $nextAssigneeIds          = [];
                }
                $steps[]              = $step;
                $reached              = false;
                $frames[$ti]['index'] = $index + 1;
                continue;
            }

            if (in_array($type, ['approval', 'fill', 'cc'], true)) {
                $steps[] = $this->makeStep($node, $armId, $iteration);
                if ($type !== 'cc') {
                    $reached = false;
                }
            }
            $frames[$ti]['index'] = $index + 1;
        }

        return [
            'steps'    => $steps,
            'trail'    => $trail,
            'cursor'   => array_values($frames),
            'complete' => $complete,
            'needs'    => $needs,
            'rounds'   => $rounds,
        ];
    }

    /**
     * Every step node in the definition, branch arms flattened in.
     *
     * @return array<array>
     */
    public function allNodes(array $definition): array
    {
        $flat = [];

        $walk = function ($nodes) use (&$walk, &$flat) {
            foreach ($nodes as $node) {
                if (($node['type'] ?? null) === 'branch') {
                    foreach ($node['branches'] ?? [] as $arm) {
                        $walk($arm['nodes'] ?? []);
                    }
                    continue;
                }
                $flat[] = $node;
            }
        };
        $walk($definition['nodes'] ?? []);

        return $flat;
    }

    /**
     * Handler ids taken from a `user` field's answer (assignee_mode 'field').
     *
     * @return array<int>
     */
    private function assigneesFromField(array $node, array $answersById): array
    {
        $fieldId = $node['assignee_field'] ?? null;
        if (!$fieldId) {
            return [];
        }

        $value = $answersById[$fieldId] ?? null;

        return array_values(array_unique(array_filter(array_map('intval', is_array($value) ? $value : [$value]))));
    }

    /**
     * Every step id inside a node range (branch arms included) — the steps a
     * round re-runs, used to decide which answers to archive and clear.
     *
     * @return array<string>
     */
    private function collectNodeIds(array $nodes): array
    {
        $ids = [];

        foreach ($nodes as $node) {
            if (($node['type'] ?? null) === 'branch') {
                foreach ($node['branches'] ?? [] as $arm) {
                    $ids = array_merge($ids, $this->collectNodeIds($arm['nodes'] ?? []));
                }
                continue;
            }
            if (!empty($node['id'])) {
                $ids[] = (string) $node['id'];
            }
        }

        return array_values(array_unique($ids));
    }

    /**
     * A branch is evaluated at runtime (when reached) rather than at submit
     * time when any arm loops or any arm condition references a deferred
     * (handler-filled) field.
     *
     * @param array $deferredFlip deferred element id => anything (flipped list)
     */
    public function isRuntimeBranch(array $node, array $deferredFlip): bool
    {
        foreach ($node['branches'] ?? [] as $arm) {
            if (!empty($arm['loop']) || !empty($arm['loop_to'])) {
                return true;
            }
            foreach ($this->conditionFieldIds($arm['when'] ?? null) as $fieldId) {
                if (isset($deferredFlip[$fieldId])) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Element ids referenced anywhere in a condition schema.
     *
     * @return array<string>
     */
    public function conditionFieldIds(?array $when): array
    {
        $ids = [];
        foreach ($when['groups'] ?? [] as $group) {
            foreach ($group['conditions'] ?? [] as $condition) {
                if (!empty($condition['field'])) {
                    $ids[] = (string) $condition['field'];
                }
            }
        }

        return array_values(array_unique($ids));
    }

    private function matchBranchArm(array $branchNode, array $answersById, bool $forceDefault): ?array
    {
        $arms = array_values($branchNode['branches'] ?? []);
        if (empty($arms)) {
            return null;
        }

        if ($forceDefault) {
            $last = $arms[count($arms) - 1];

            return empty($last['when']) ? $last : null;
        }

        foreach ($arms as $i => $arm) {
            $isLast = $i === count($arms) - 1;
            $when   = $arm['when'] ?? null;
            if ((empty($when) && $isLast) || (!empty($when) && $this->conditions->evaluate($when, $answersById))) {
                return $arm;
            }
        }

        return null;
    }

    private function trailEntry(string $branchId, ?array $matched, int $iteration, bool $forced): array
    {
        $entry = [
            'branch_node'  => $branchId,
            'matched'      => $matched['id'] ?? null,
            'matched_name' => $matched['name'] ?? null,
            'iteration'    => $iteration,
            'at'           => date('Y-m-d H:i:s'),
        ];
        if ($forced) {
            $entry['forced_default'] = true; // safety cap tripped — auditable in the snapshot
        }

        return $entry;
    }

    private function makeStep(array $node, ?string $sourceBranchId, int $iteration): array
    {
        $type = $node['type'] ?? null;
        $step = [
            'node_id'           => $node['id'] ?? null,
            'type'              => $type,
            'name'              => $node['name'] ?? ($type === 'approval' ? 'Approval' : ($type === 'fill' ? 'Fill Section' : 'Notify')),
            'field_permissions' => $node['field_permissions'] ?? ['default' => 'read'],
            'source_branch_id'  => $sourceBranchId,
            'iteration'         => $iteration,
        ];

        if ($type === 'approval') {
            $step['approver_ids']  = array_values(array_filter(array_map('intval', $node['approver_ids'] ?? [])));
            $step['approval_mode'] = in_array($node['approval_mode'] ?? 'any', ['any', 'all'], true)
                                     ? ($node['approval_mode'] ?? 'any') : 'any';
        } elseif ($type === 'fill') {
            $step['approver_ids']  = array_values(array_filter(array_map('intval', $node['assignee_ids'] ?? [])));
            $step['approval_mode'] = 'any';
        } elseif ($type === 'cc') {
            $step['user_ids'] = array_values(array_filter(array_map('intval', $node['user_ids'] ?? [])));
        }

        return $step;
    }

    private function frameNodes(array $definition, array $frame): array
    {
        if (($frame['scope'] ?? 'root') === 'root') {
            return array_values($definition['nodes'] ?? []);
        }

        $branchNode = $this->findBranchNode($definition['nodes'] ?? [], (string) ($frame['branch_node_id'] ?? ''));
        $arm        = $branchNode ? $this->findArm($branchNode, (string) ($frame['arm_id'] ?? '')) : null;

        return array_values($arm['nodes'] ?? []);
    }

    private function findBranchNode(array $nodes, string $id): ?array
    {
        foreach ($nodes as $node) {
            if (($node['type'] ?? null) !== 'branch') {
                continue;
            }
            if (($node['id'] ?? null) === $id) {
                return $node;
            }
            foreach ($node['branches'] ?? [] as $arm) {
                $found = $this->findBranchNode($arm['nodes'] ?? [], $id);
                if ($found) {
                    return $found;
                }
            }
        }

        return null;
    }

    private function findArm(array $branchNode, string $armId): ?array
    {
        foreach ($branchNode['branches'] ?? [] as $arm) {
            if (($arm['id'] ?? null) === $armId) {
                return $arm;
            }
        }

        return null;
    }

    private function containsBlockingNode(array $nodes): bool
    {
        foreach ($nodes as $node) {
            $type = $node['type'] ?? null;
            if (in_array($type, ['approval', 'fill'], true)) {
                return true;
            }
            if ($type === 'branch') {
                foreach ($node['branches'] ?? [] as $arm) {
                    if ($this->containsBlockingNode($arm['nodes'] ?? [])) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /** Position of a node id in a node list, or null when absent. */
    private function indexOfNode(array $nodes, string $id): ?int
    {
        foreach (array_values($nodes) as $index => $node) {
            if (($node['id'] ?? null) === $id) {
                return $index;
            }
        }

        return null;
    }

    /**
     * A repeated range blanks every field its handlers fill. If one of those
     * fields is also the person field another handler in the range is
     * assigned from, somebody earlier in the range must refill it — otherwise
     * the next round has nobody to hand the step to.
     *
     * @return array list of error strings
     */
    private function rangeAssigneeFieldErrors(array $range, string $armName, array $schema, FormSchemaService $schemaService): array
    {
        $errors = [];
        $range  = array_values($range);

        // element id => first index in the range that fills it
        $filledAt = [];
        foreach ($range as $index => $node) {
            if (($node['type'] ?? null) !== 'fill') {
                continue;
            }
            $permissions = $node['field_permissions'] ?? [];
            $permissions['default'] = 'read';
            foreach ($schemaService->resolveFieldPermissions($schema, $permissions) as $elementId => $level) {
                if ($level === 'edit' && !isset($filledAt[$elementId])) {
                    $filledAt[$elementId] = $index;
                }
            }
        }

        foreach ($range as $index => $node) {
            if (($node['type'] ?? null) !== 'fill' || ($node['assignee_mode'] ?? 'fixed') !== 'field') {
                continue;
            }
            $field = $node['assignee_field'] ?? null;
            if ($field === null || !isset($filledAt[$field])) {
                continue; // filled outside the range (e.g. at submit): survives the reset
            }
            if ($filledAt[$field] >= $index) {
                $errors[] = 'Branch "' . $armName . '" repeats "' . ($node['name'] ?? 'a handler')
                    . '", but the person field it is assigned from is cleared each round and no earlier step in the repeat fills it again.';
            }
        }

        return $errors;
    }

    /**
     * A loop can only terminate if some fill step in the repeated range can
     * edit at least one field the loop condition checks.
     */
    private function rangeCanChangeCondition(array $range, ?array $when, array $schema, FormSchemaService $schemaService): bool
    {
        $conditionFields = $this->conditionFieldIds($when);
        if (empty($conditionFields)) {
            return true;
        }
        $flip  = array_flip($conditionFields);
        $found = false;

        $walk = function ($nodes) use (&$walk, &$found, $flip, $schema, $schemaService) {
            foreach ($nodes as $node) {
                if ($found) {
                    return;
                }
                $type = $node['type'] ?? null;
                if ($type === 'branch') {
                    foreach ($node['branches'] ?? [] as $sub) {
                        $walk($sub['nodes'] ?? []);
                    }
                    continue;
                }
                if ($type !== 'fill') {
                    continue;
                }
                $permissions = $node['field_permissions'] ?? [];
                $permissions['default'] = 'read'; // only explicit "edit" counts, as in deferredFieldIds()
                foreach ($schemaService->resolveFieldPermissions($schema, $permissions) as $elementId => $level) {
                    if ($level === 'edit' && isset($flip[$elementId])) {
                        $found = true;

                        return;
                    }
                }
            }
        };
        $walk($range);

        return $found;
    }

    /**
     * Flat summary of the definition for API payloads (top-level walk,
     * branches summarised with their branch names).
     */
    public function summary(array $definition): array
    {
        $summary = [];

        foreach ($definition['nodes'] ?? [] as $node) {
            $type = $node['type'] ?? null;

            if ($type === 'branch') {
                $summary[] = [
                    'node_id'  => $node['id'] ?? null,
                    'type'     => 'branch',
                    'branches' => collect($node['branches'] ?? [])->map(fn ($b) => [
                        'id'    => $b['id'] ?? null,
                        'name'  => $b['name'] ?? null,
                        'steps' => collect($b['nodes'] ?? [])->map(fn ($n) => [
                            'node_id' => $n['id'] ?? null,
                            'type'    => $n['type'] ?? null,
                            'name'    => $n['name'] ?? null,
                        ])->values()->all(),
                    ])->values()->all(),
                ];
            } else {
                $summary[] = [
                    'node_id' => $node['id'] ?? null,
                    'type'    => $type,
                    'name'    => $node['name'] ?? null,
                ];
            }
        }

        return $summary;
    }
}
