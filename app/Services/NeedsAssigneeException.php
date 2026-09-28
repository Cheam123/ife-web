<?php

namespace App\Services;

/**
 * Thrown by the approval engine when the walk pauses at a runtime-assignee
 * handler and the acting user has not picked who handles it. Callers roll
 * back the action and ask the client to re-submit with next_assignee_ids.
 */
class NeedsAssigneeException extends \RuntimeException
{
    /** @var array{type: string, node_id: ?string, name: string} */
    public array $node;

    public function __construct(array $node)
    {
        parent::__construct('A handler must be chosen for the next step.');
        $this->node = $node;
    }
}
