{{-- Read-only rendering of one process node (preview page). Params: $node, $approverNames --}}
@php
    $names = function ($ids) use ($approverNames) {
        return collect($ids ?? [])->map(fn ($id) => $approverNames[(int) $id] ?? ('User #' . $id))->implode(', ');
    };
@endphp

@if(($node['type'] ?? '') === 'approval')
    <div class="border rounded p-2 mb-2" style="background:#fdf3e7;">
        <div class="fw-semibold small"><i class="mdi mdi-account-check-outline me-1"></i>{{ $node['name'] ?? 'Approval' }}</div>
        <div class="small text-muted">{{ $names($node['approver_ids'] ?? []) }}</div>
        <div class="small text-muted fst-italic">{{ ($node['approval_mode'] ?? 'any') === 'all' ? 'Everyone must approve' : 'Any one approver' }}</div>
    </div>
@elseif(($node['type'] ?? '') === 'fill')
    <div class="border rounded p-2 mb-2" style="background:#f1ecfb;">
        <div class="fw-semibold small"><i class="mdi mdi-account-edit-outline me-1"></i>{{ $node['name'] ?? 'Handler' }}</div>
        <div class="small text-muted">
            Handler:
            @if(($node['assignee_mode'] ?? 'fixed') === 'runtime')
                <span class="fst-italic">picked when the flow reaches this step</span>
            @elseif(($node['assignee_mode'] ?? 'fixed') === 'field')
                <span class="fst-italic">whoever is chosen in the form</span>
            @else
                {{ $names($node['assignee_ids'] ?? []) }}
            @endif
        </div>
        <div class="small text-muted fst-italic">Fills their section of the form</div>
    </div>
@elseif(($node['type'] ?? '') === 'cc')
    <div class="border rounded p-2 mb-2" style="background:#f2fbf4;">
        <div class="fw-semibold small"><i class="mdi mdi-email-outline me-1"></i>{{ $node['name'] ?? 'Notify' }}</div>
        <div class="small text-muted">{{ $names($node['user_ids'] ?? []) }}</div>
    </div>
@elseif(($node['type'] ?? '') === 'branch')
    <div class="border rounded p-2 mb-2" style="background:#fff8ec;border-style:dashed!important;">
        <div class="fw-semibold small mb-1"><i class="mdi mdi-source-branch me-1"></i>Conditional branch</div>
        @foreach($node['branches'] ?? [] as $branch)
            <div class="border rounded p-2 mb-1 bg-white">
                <div class="small fw-semibold">
                    {{ ($branch['when'] ?? null) ? ($branch['name'] ?? 'Branch') : 'Else' }}
                </div>
                @forelse($branch['nodes'] ?? [] as $child)
                    @include('page.form.partials._process-preview-node', ['node' => $child, 'approverNames' => $approverNames])
                @empty
                    <div class="small text-muted fst-italic">No steps — continues directly.</div>
                @endforelse
            </div>
        @endforeach
    </div>
@endif
