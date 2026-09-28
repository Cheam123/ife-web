{{--
    Stage-based approval progress timeline.
    Params: $submission (with approvals.actedBy loaded)
--}}
@php
    $stages      = $submission->approvals;
    $branchTrail = collect($submission->process_snapshot['branch_trail'] ?? [])
                     ->filter(fn ($t) => !empty($t['matched_name']) && strtolower($t['matched_name'] ?? '') !== 'else');
@endphp

@if($stages->isNotEmpty() || $submission->rejected_by)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header border-bottom py-3 px-4"
         style="background: linear-gradient(135deg, #f8f9ff 0%, #f0f4ff 100%);">
        <h6 class="mb-0 fw-bold">
            <i class="mdi mdi-timeline-check-outline me-1 text-primary"></i>Approval Progress
        </h6>
    </div>
    <div class="card-body px-4 py-3">

        @if($branchTrail->isNotEmpty())
            <div class="alert alert-light border py-2 px-3 mb-3 small">
                <i class="mdi mdi-source-branch me-1 text-warning"></i>
                Route taken:
                @foreach($branchTrail as $trail)
                    <strong>{{ $trail['matched_name'] }}</strong>{{ !$loop->last ? ' → ' : '' }}
                @endforeach
            </div>
        @endif

        @php $reached = true; @endphp
        @foreach($stages as $stage)
            @php
                $isCc   = $stage->node_type === 'cc';
                $isFill = $stage->isFillNode();
                $done   = in_array($stage->status, ['approved', 'completed']);
                $badgeClass = 'bg-secondary';
                if ($isCc) {
                    $badgeClass = 'bg-info';
                } elseif ($done) {
                    $badgeClass = 'bg-success';
                } elseif ($stage->status === 'rejected') {
                    $badgeClass = 'bg-danger';
                } elseif ($stage->status === 'pending' && $reached) {
                    $badgeClass = 'bg-warning';
                }
            @endphp
            <div class="d-flex gap-3 {{ !$loop->last ? 'mb-3' : '' }}">
                <div class="flex-shrink-0 mt-1">
                    <span class="badge rounded-circle {{ $badgeClass }}"
                          style="width:22px;height:22px;display:inline-flex;align-items:center;justify-content:center;">
                        @if($isCc)
                            <i class="mdi mdi-email-outline" style="font-size:10px;"></i>
                        @elseif($done)
                            <i class="mdi mdi-check" style="font-size:10px;"></i>
                        @elseif($stage->status === 'rejected')
                            <i class="mdi mdi-close" style="font-size:10px;"></i>
                        @elseif($isFill)
                            <i class="mdi mdi-pencil-outline" style="font-size:10px;"></i>
                        @else
                            {{ $stage->sequence }}
                        @endif
                    </span>
                </div>
                <div>
                    <div class="fw-semibold small">
                        {{ $stage->name }}
                        @if($isFill)
                            <span class="badge bg-light text-dark">handler</span>
                        @elseif(!$isCc && $stage->approval_mode === 'all')
                            <span class="badge bg-light text-dark">everyone</span>
                        @endif
                        @if((int) $stage->iteration >= 2)
                            <span class="badge bg-light text-dark">round {{ $stage->iteration }}</span>
                        @endif
                    </div>

                    @if($isCc)
                        <div class="text-muted small">Notified</div>
                    @elseif($stage->status === 'completed')
                        <div class="text-success small">Section completed by <strong>{{ optional($stage->actedBy)->name ?? 'N/A' }}</strong></div>
                        <div class="text-muted" style="font-size:0.75rem;">{{ $stage->acted_at?->format('d M Y, h:i A') }}</div>
                    @elseif($stage->status === 'approved')
                        <div class="text-success small">Approved by <strong>{{ optional($stage->actedBy)->name ?? 'N/A' }}</strong></div>
                        <div class="text-muted" style="font-size:0.75rem;">{{ $stage->acted_at?->format('d M Y, h:i A') }}</div>
                        @if($stage->remark)
                            <div class="text-muted fst-italic mt-1" style="font-size:0.8rem;">"{{ $stage->remark }}"</div>
                        @endif
                    @elseif($stage->status === 'rejected')
                        <div class="text-danger small">Rejected by <strong>{{ optional($stage->actedBy)->name ?? 'N/A' }}</strong></div>
                        <div class="text-muted" style="font-size:0.75rem;">{{ $stage->acted_at?->format('d M Y, h:i A') }}</div>
                        @if($stage->remark)
                            <div class="text-muted fst-italic mt-1" style="font-size:0.8rem;">"{{ $stage->remark }}"</div>
                        @endif
                    @elseif($stage->status === 'pending' && $reached)
                        <div class="text-warning small">{{ $isFill ? 'Awaiting section completion' : 'Awaiting approval' }}</div>
                        @if(!$isFill && $stage->approval_mode === 'all' && !empty($stage->actions))
                            <div class="text-muted" style="font-size:0.75rem;">
                                {{ count($stage->actions) }} of {{ count($stage->approver_ids ?? []) }} approvers have approved
                            </div>
                        @endif
                    @else
                        <div class="text-muted small">Waiting for earlier steps</div>
                    @endif
                </div>
            </div>
            @php
                if ($stage->isBlockingNode() && !$done) { $reached = false; }
            @endphp
        @endforeach

        @if($submission->status === 'pending'
            && isset($submission->process_snapshot['cursor'])
            && empty($submission->process_snapshot['complete']))
            {{-- Unresolved runtime branch / loop: later steps aren't known yet. --}}
            <div class="d-flex gap-3 mt-3">
                <div class="flex-shrink-0 mt-1">
                    <span class="badge rounded-circle bg-light text-muted border"
                          style="width:22px;height:22px;display:inline-flex;align-items:center;justify-content:center;border-style:dashed!important;">
                        <i class="mdi mdi-dots-horizontal" style="font-size:10px;"></i>
                    </span>
                </div>
                <div>
                    <div class="fw-semibold small text-muted">More steps may follow</div>
                    <div class="text-muted" style="font-size:0.75rem;">Decided by the answers as the flow continues</div>
                </div>
            </div>
        @endif

        @if($submission->rejected_by && $stages->where('status', 'rejected')->isEmpty())
            <div class="d-flex gap-3 mt-3">
                <div class="flex-shrink-0 mt-1">
                    <span class="badge rounded-circle bg-danger" style="width:22px;height:22px;display:inline-flex;align-items:center;justify-content:center;"><i class="mdi mdi-close" style="font-size:10px;"></i></span>
                </div>
                <div>
                    <div class="fw-semibold small text-danger">Rejected</div>
                    <div class="text-danger small">By <strong>{{ $submission->rejectedBy?->name }}</strong></div>
                    @if($submission->rejected_remark)
                        <div class="text-muted fst-italic mt-1" style="font-size:0.8rem;">"{{ $submission->rejected_remark }}"</div>
                    @endif
                </div>
            </div>
        @endif

    </div>
</div>
@endif
