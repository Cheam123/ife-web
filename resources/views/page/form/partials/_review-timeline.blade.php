{{--
    Vertical connected-line approval/handler progress timeline for the
    redesigned review page. Page-scoped to admin-view.blade.php (the
    shared _approval-timeline.blade.php partial keeps its own look for
    submission-view.blade.php).
    Params: $submission (approvals.actedBy loaded), $canApprove, $waitingFor
--}}
@php
    $blockingStages = $submission->approvals->filter(fn ($row) => $row->isBlockingNode())->values();
    $currentStage   = $submission->currentStage();
    $hasRejectedRow = $blockingStages->contains(fn ($row) => $row->status === 'rejected');
    $showLegacyRejected = $submission->rejected_by && !$hasRejectedRow;
    // A runtime branch / loop may still add stages: show a tentative tail step.
    $processOpen    = $submission->status === 'pending'
        && isset($submission->process_snapshot['cursor'])
        && empty($submission->process_snapshot['complete']);
    $totalSteps     = 1 + $blockingStages->count() + ($showLegacyRejected ? 1 : 0) + ($processOpen ? 1 : 0);
@endphp

<div class="rs-timeline">
    {{-- Step 1: Submitted --}}
    <div class="rs-step">
        <div class="rs-step-rail">
            <div class="rs-step-dot rs-step-dot--done">&#10003;</div>
            @if($totalSteps > 1)<div class="rs-step-line rs-step-line--done"></div>@endif
        </div>
        <div class="rs-step-body">
            <div class="rs-step-title">Submitted</div>
            <div class="rs-step-sub rs-step-sub--muted">
                {{ optional($submission->submittedBy)->name ?? 'Unknown' }} &middot; {{ $submission->created_at->format('d M') }}
            </div>
        </div>
    </div>

    @foreach($blockingStages as $stage)
        @php
            $isFill = $stage->isFillNode();
            $done   = in_array($stage->status, ['approved', 'completed'], true);
            $isRejected = $stage->status === 'rejected';
            // Once the submission is no longer pending, nothing further down the
            // chain is "in progress" — a rejection stops the chain, so later
            // untouched rows are just unreached, not current.
            $isCurrent  = $submission->status === 'pending' && $currentStage && $currentStage->id === $stage->id;
            $dotClass = $isRejected ? 'rs-step-dot--rejected' : ($done ? 'rs-step-dot--done' : ($isCurrent ? 'rs-step-dot--current' : 'rs-step-dot--pending'));
            $lineClass = $done ? 'rs-step-line--done' : 'rs-step-line--muted';
        @endphp
        <div class="rs-step">
            <div class="rs-step-rail">
                <div class="rs-step-dot {{ $dotClass }}">
                    @if($isRejected)&#10005;@elseif($done)&#10003;@endif
                </div>
                @if(!$loop->last || $showLegacyRejected || $processOpen)<div class="rs-step-line {{ $lineClass }}"></div>@endif
            </div>
            <div class="rs-step-body">
                <div class="rs-step-title {{ $isCurrent ? 'rs-step-title--current' : '' }}">
                    {{ $stage->name }}
                    @if($isFill)<span class="rs-step-tag">handler</span>@elseif($stage->approval_mode === 'all')<span class="rs-step-tag">everyone</span>@endif
                    @if((int) $stage->iteration >= 2)<span class="rs-step-tag">round {{ $stage->iteration }}</span>@endif
                </div>

                @if($done)
                    <div class="rs-step-sub rs-step-sub--done">
                        {{ optional($stage->actedBy)->name ?? 'N/A' }} &middot; {{ $stage->acted_at?->format('d M') }}
                        &middot; {{ $isFill ? 'Completed' : 'Approved' }}
                    </div>
                @elseif($isRejected)
                    <div class="rs-step-sub rs-step-sub--rejected">
                        {{ optional($stage->actedBy)->name ?? 'N/A' }} &middot; {{ $stage->acted_at?->format('d M') }} &middot; Rejected
                    </div>
                    @if($stage->remark)
                        <div class="rs-step-remark">&quot;{{ $stage->remark }}&quot;</div>
                    @endif
                @elseif($isCurrent)
                    <div class="rs-step-sub rs-step-sub--current">
                        In progress &mdash;
                        @if($canApprove) you
                        @elseif($waitingFor->isNotEmpty()) {{ $waitingFor->pluck('name')->implode(', ') }}
                        @else pending
                        @endif
                    </div>
                @else
                    <div class="rs-step-sub rs-step-sub--muted">Pending</div>
                @endif
            </div>
        </div>
    @endforeach

    @if($showLegacyRejected)
        <div class="rs-step">
            <div class="rs-step-rail">
                <div class="rs-step-dot rs-step-dot--rejected">&#10005;</div>
            </div>
            <div class="rs-step-body">
                <div class="rs-step-title">Rejected</div>
                <div class="rs-step-sub rs-step-sub--rejected">By {{ $submission->rejectedBy?->name }}</div>
                @if($submission->rejected_remark)
                    <div class="rs-step-remark">&quot;{{ $submission->rejected_remark }}&quot;</div>
                @endif
            </div>
        </div>
    @endif

    @if($processOpen)
        {{-- Unresolved runtime branch / loop: later steps aren't known yet. --}}
        <div class="rs-step">
            <div class="rs-step-rail">
                <div class="rs-step-dot rs-step-dot--pending" style="border-style: dashed;"></div>
            </div>
            <div class="rs-step-body">
                <div class="rs-step-title" style="color: #98a2b3;">More steps may follow</div>
                <div class="rs-step-sub rs-step-sub--muted">Decided by the answers as the flow continues</div>
            </div>
        </div>
    @endif
</div>
