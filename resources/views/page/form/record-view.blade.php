@extends('layouts.master')

@section('title') Review Submission @endsection

@section('css')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@include('page.form.partials._table-style')
@endsection

@section('content')
<style>
    /* ===== Review Submission — page-scoped redesign ===== */
    .rs-page { background:#eef1f6; border-radius:16px; padding:24px 24px 40px;
               font-family:'Public Sans','Helvetica Neue',sans-serif; color:#16203a; }
    /* Exclude .btn — an anchor styled as a button must keep its button colours.
       `.rs-page a` outranks `.btn-primary` on specificity, which was repainting
       the label of every link-button in the page's link blue. */
    .rs-page a:not(.btn) { color:#4a5fd8; text-decoration:none; }
    .rs-page a:not(.btn):hover { color:#3446b8; }

    /* Buttons — the page accent rather than the theme default, so link-buttons
       and real <button>s look identical. */
    .rs-page .btn { font-weight:600; font-size:13.5px; border-radius:9px; padding:8px 16px;
                    transition:background-color .15s ease, border-color .15s ease, color .15s ease; }
    .rs-page .btn:focus, .rs-page .btn:focus-visible { box-shadow:0 0 0 3px rgba(74,95,216,0.18); }
    .rs-page .btn-primary { background:#4a5fd8; border-color:#4a5fd8; color:#fff; }
    .rs-page .btn-primary:hover, .rs-page .btn-primary:focus, .rs-page .btn-primary:active {
        background:#3446b8; border-color:#3446b8; color:#fff; }
    .rs-page .btn-outline-primary { background:#fff; border-color:#c7d0f5; color:#3446b8; }
    .rs-page .btn-outline-primary:hover, .rs-page .btn-outline-primary:focus, .rs-page .btn-outline-primary:active {
        background:#eef1fd; border-color:#4a5fd8; color:#2c3ba0; }
    .rs-page .btn-outline-secondary { background:#fff; border-color:#d5dbe7; color:#3a445c; }
    .rs-page .btn-outline-secondary:hover, .rs-page .btn-outline-secondary:focus, .rs-page .btn-outline-secondary:active {
        background:#f1f4f9; border-color:#b9c2d4; color:#16203a; }
    .rs-page .btn-outline-danger { background:#fff; border-color:#f2c9c4; color:#c0392b; }
    .rs-page .btn-outline-danger:hover, .rs-page .btn-outline-danger:focus, .rs-page .btn-outline-danger:active {
        background:#fdeeee; border-color:#c0392b; color:#a5301f; }

    /* Topbar */
    .rs-topbar { background:#fff; border:1px solid #e3e8f0; border-radius:14px; padding:20px 26px; margin-bottom:24px;
                 display:flex; align-items:center; justify-content:space-between; gap:24px; flex-wrap:wrap; }
    .rs-back { font-size:13px; font-weight:600; display:inline-flex; align-items:center; gap:5px; margin-bottom:8px; }
    .rs-title-row { display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
    .rs-title { margin:0; font-size:22px; font-weight:800; letter-spacing:-0.01em; color:#16203a; }
    .rs-id-badge { font-size:12px; font-weight:600; color:#6b7690; background:#f1f4f9; border:1px solid #e3e8f0; padding:3px 9px; border-radius:6px; }
    .rs-status-pill { font-size:12px; font-weight:700; padding:4px 11px; border-radius:999px; display:inline-flex; align-items:center; gap:6px; }
    .rs-status-dot { width:6px; height:6px; border-radius:999px; display:inline-block; }
    .rs-form-desc { font-size:13px; color:#6b7690; margin-top:6px; max-width:640px; }
    .rs-submitter { display:flex; align-items:center; gap:10px; font-size:13px; color:#6b7690; }
    .rs-avatar { width:32px; height:32px; border-radius:999px; background:#dfe6fb; color:#3446b8; font-weight:700; font-size:13px;
                 display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .rs-submitter-name { font-weight:600; color:#16203a; }
    .rs-submitter-date { font-size:12px; }

    /* Grid */
    .rs-grid { display:grid; grid-template-columns:minmax(0,1fr) 352px; gap:24px; align-items:start; }
    .rs-left { display:flex; flex-direction:column; gap:24px; min-width:0; }
    .rs-right { display:flex; flex-direction:column; gap:20px; position:sticky; top:24px; }
    @media (max-width: 991.98px) { .rs-grid { grid-template-columns:1fr; } .rs-right { position:static; } }

    /* Cards */
    .rs-card { background:#fff; border:1px solid #e3e8f0; border-radius:14px;
               box-shadow:0 1px 2px rgba(16,24,40,0.04), 0 6px 16px -8px rgba(16,24,40,0.08); overflow:hidden; }
    .rs-card-head { padding:18px 28px; border-bottom:1px solid #eceff5; display:flex; align-items:center; justify-content:space-between; gap:12px; }
    .rs-card-title { font-size:15px; font-weight:700; }
    .rs-card-meta { font-size:12.5px; color:#8a93a8; white-space:nowrap; }
    .rs-card-body { padding:8px 28px 12px; }
    .rs-empty { text-align:center; padding:48px 0; color:#8a93a8; }
    .rs-empty i { font-size:40px; opacity:.4; display:block; margin-bottom:8px; }

    .rs-section { padding:20px 0 8px; }
    .rs-section-head { display:flex; align-items:center; gap:12px; margin-bottom:18px; }
    .rs-section-label { font-size:11.5px; font-weight:800; letter-spacing:0.09em; text-transform:uppercase; color:#4a5fd8; }
    .rs-section-head::after { content:''; flex:1; height:1px; background:#eceff5; }
    .rs-fields-grid { display:grid; grid-template-columns:1fr 1fr; gap:20px 32px; }
    @media (max-width: 575.98px) { .rs-fields-grid { grid-template-columns:1fr; } }

    .rs-field-wide { grid-column:1 / -1; }
    .rs-field-label { font-size:11px; font-weight:700; letter-spacing:0.07em; text-transform:uppercase; color:#8a93a8; margin-bottom:5px; }
    .rs-field-value { font-size:14.5px; font-weight:600; }
    .rs-field-text { font-size:14px; line-height:1.65; color:#3a445c; white-space:pre-wrap; margin:0; }
    .rs-field-empty { color:#b6bec7; font-style:italic; }
    .rs-tag-list { display:flex; flex-wrap:wrap; gap:8px; margin-top:2px; }
    .rs-tag { font-size:12.5px; font-weight:600; color:#3446b8; background:#eef1fd; border:1px solid #dfe6fb; padding:4px 12px; border-radius:999px; }
    .rs-check-list { display:flex; flex-direction:column; gap:6px; margin-top:2px; }
    .rs-check-item { display:flex; align-items:center; gap:9px; font-size:14px; color:#3a445c; }
    .rs-check-icon { width:17px; height:17px; border-radius:5px; background:#fff; border:1.5px solid #d5dbe7;
                     display:inline-flex; align-items:center; justify-content:center; flex-shrink:0; font-size:11px; color:#fff; }
    .rs-check-icon-on { background:#4a5fd8; border-color:#4a5fd8; font-weight:800; }

    /* Fill / Handler section — keeps the app-wide Handler purple accent */
    #fill-section-card { border-left:4px solid #7a56d1 !important; }
    .rs-fill-badge { font-size:11.5px; font-weight:700; letter-spacing:0.06em; text-transform:uppercase; color:#7a56d1;
                     background:#f1ecfb; padding:5px 12px; border-radius:999px; white-space:nowrap; }
    .rs-fill-grid { display:grid; grid-template-columns:1fr 1fr; gap:20px 28px; }
    @media (max-width: 575.98px) { .rs-fill-grid { grid-template-columns:1fr; } }
    .rs-fill-grid > .form-group-wrapper { grid-column:1 / -1; border:none !important; box-shadow:none !important; background:transparent !important; }
    .rs-fill-grid > .form-group-wrapper .card-header { background:transparent !important; border:none !important; padding:0 0 14px !important; }
    .rs-fill-grid > .form-group-wrapper .card-header h6 { color:#7a56d1 !important; font-size:11.5px !important; text-transform:uppercase;
                     letter-spacing:.09em; font-weight:800 !important; border-bottom:1px solid #eceff5; padding-bottom:12px; }
    .rs-fill-grid > .form-group-wrapper .card-body { display:grid; grid-template-columns:1fr 1fr; gap:20px 28px; padding:0 !important; }
    @media (max-width: 575.98px) { .rs-fill-grid > .form-group-wrapper .card-body { grid-template-columns:1fr; } }
    #fill-section-card .form-element-wrapper[data-type="textarea"],
    #fill-section-card .form-element-wrapper[data-type="file"],
    #fill-section-card .form-element-wrapper[data-type="multi-select"],
    #fill-section-card .form-element-wrapper[data-type="multi-choice"],
    #fill-section-card .form-element-wrapper[data-type="checkbox"],
    #fill-section-card .form-element-wrapper[data-kind="description"] { grid-column:1 / -1; }
    #fill-section-card .form-control, #fill-section-card .form-select { border:1px solid #d5dbe7; border-radius:9px; padding:10px 13px; font-size:14px; }
    #fill-section-card .form-control:focus, #fill-section-card .form-select:focus {
        border-color:#7a56d1 !important; box-shadow:0 0 0 3px rgba(122,86,209,0.14) !important; }
    #fill-section-card .form-label { font-size:13px; font-weight:700; color:#16203a; }

    @keyframes fillAttnPulse { 0% { box-shadow:0 0 0 0 rgba(122,86,209,0.45); } 100% { box-shadow:0 0 0 14px rgba(122,86,209,0); } }
    #fill-section-card.fill-attn { animation:fillAttnPulse 1.1s ease-out 2; }

    /* Sidebar cards */
    .rs-sidebar-card { padding:22px 24px; }
    .rs-sidebar-title { font-size:13px; font-weight:800; letter-spacing:0.04em; text-transform:uppercase; color:#6b7690; margin-bottom:18px; }
    .rs-actions { display:flex; flex-direction:column; gap:9px; }
    .rs-actions-help { font-size:12.5px; color:#8a93a8; margin-top:14px; line-height:1.5; }
    .rs-waiting-card { background:#fff8ec; border:1px solid #f2e2c0; border-radius:14px; padding:18px 22px; font-size:13.5px; color:#7a5a12; line-height:1.55; }
    .rs-waiting-card span { color:#9a7b34; }

    /* Why an action isn't offered, said out loud instead of an absent button */
    /* "Last round" hint under a fill input. Muted and inert on purpose — it is
       reference, not an answer; the input above it is the one that counts. */
    .rs-prev { margin-top:7px; padding:8px 11px; border-radius:9px;
               background:#f7f5fd; border:1px solid #e7e1f7; }
    .rs-prev-head { display:flex; align-items:center; gap:6px; font-size:11.5px;
                    font-weight:600; color:#7a56d1; margin-bottom:3px; }
    .rs-prev-head i { font-size:13px; }
    .rs-prev-value { font-size:13px; color:#3a445c; white-space:pre-wrap; word-break:break-word; }

    .rs-hint { font-size:12.5px; font-weight:600; color:#8a93a8; background:#f1f4f9; border:1px solid #e3e8f0;
               border-radius:999px; padding:6px 13px; white-space:nowrap; }

    /* Closed-record notice */
    .rs-closed-card { background:#f7f8fb; border:1px solid #e3e8f0; border-radius:14px; padding:16px 22px; margin-bottom:24px; }
    .rs-closed-head { font-size:13.5px; font-weight:700; color:#16203a; display:flex; align-items:center; gap:8px; }
    .rs-closed-remark { font-size:13.5px; color:#3a445c; line-height:1.55; margin-top:8px; white-space:pre-wrap; }
    .rs-closed-meta { font-size:12px; color:#8a93a8; margin-top:8px; }

    /* Timeline */
    .rs-route-note { background:#f7f8fb; border:1px solid #eceff5; border-radius:10px; padding:10px 14px; font-size:12.5px; color:#3a445c; margin-bottom:16px; }
    .rs-timeline { display:flex; flex-direction:column; }
    .rs-step { display:flex; gap:14px; }
    .rs-step-rail { display:flex; flex-direction:column; align-items:center; }
    .rs-step-dot { width:22px; height:22px; border-radius:999px; font-size:11px; font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0; color:#fff; }
    .rs-step-dot--done { background:#178a50; }
    .rs-step-dot--rejected { background:#c0392b; }
    .rs-step-dot--current { background:#fff; border:2px solid #4a5fd8; box-shadow:0 0 0 4px rgba(74,95,216,0.14); }
    .rs-step-dot--pending { background:#fff; border:2px solid #d5dbe7; }
    .rs-step-line { width:2px; flex:1; min-height:22px; }
    .rs-step-line--done { background:#178a50; }
    .rs-step-line--muted { background:#d5dbe7; }
    .rs-step-body { padding-bottom:18px; }
    .rs-step-title { font-size:14px; font-weight:700; }
    .rs-step-title--current { color:#4a5fd8; }
    .rs-step-tag { font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:.03em; color:#8a93a8;
                   background:#f1f4f9; border-radius:5px; padding:2px 6px; margin-left:6px; vertical-align:middle; }
    .rs-step-sub { font-size:12.5px; margin-top:2px; }
    .rs-step-sub--muted { color:#8a93a8; }
    .rs-step-sub--done { color:#178a50; }
    .rs-step-sub--rejected { color:#c0392b; }
    .rs-step-sub--current { color:#4a5fd8; font-weight:600; }
    .rs-step-remark { font-size:12.5px; color:#6b7690; font-style:italic; margin-top:4px; }

    /* Modals */
    .rs-modal { border-radius:16px; overflow:hidden; }
    .rs-modal .modal-body { padding:24px 28px 4px; }
    .rs-modal .modal-footer { padding:18px 28px 24px; border-top:0; }
    .rs-modal-icon { width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:20px; font-weight:800; margin-bottom:14px; }
    .rs-modal-icon--success { background:#e8f6ef; color:#178a50; }
    .rs-modal-icon--danger { background:#fdeeee; color:#c0392b; }
    .rs-modal-title { font-size:17px; font-weight:800; }
    .rs-modal-desc { font-size:13.5px; color:#6b7690; margin-top:6px; line-height:1.55; }
</style>

<div class="rs-page">

    @php
        $statusMap = [
            'pending'   => ['label' => 'In Review', 'bg' => '#eef1fd', 'color' => '#3446b8'],
            'approved'  => ['label' => 'Approved',  'bg' => '#e8f6ef', 'color' => '#178a50'],
            'rejected'  => ['label' => 'Rejected',  'bg' => '#fdeeee', 'color' => '#c0392b'],
            'cancelled' => ['label' => 'Cancelled', 'bg' => '#f1f4f9', 'color' => '#6b7690'],
        ];
        $rsStatus = $statusMap[$submission->status] ?? $statusMap['pending'];
        $rsSubId  = 'SUB-' . str_pad($submission->id, 4, '0', STR_PAD_LEFT);
    @endphp

    {{-- Topbar: the case, its links, and the actions available on it --}}
    <div class="rs-topbar">
        <div>
            <a href="{{ route('form.records.index') }}" class="rs-back">
                <i class="mdi mdi-arrow-left"></i> Back to records
            </a>
            <div class="rs-title-row">
                <h1 class="rs-title">{{ $recordTitle }}</h1>
                <span class="rs-id-badge">{{ $root->recordReference() }}</span>
                <span class="rs-status-pill" style="background:{{ $rsStatus['bg'] }};color:{{ $rsStatus['color'] }};">
                    <span class="rs-status-dot" style="background:{{ $rsStatus['color'] }};"></span>{{ $rsStatus['label'] }}
                </span>
                @unless($root->isRecordOpen())
                    <span class="rs-status-pill" style="background:#f1f4f9;color:#6b7690;">Record closed</span>
                @endunless
            </div>
            <div class="rs-form-desc">
                {{ $submission->form->name ?? 'N/A' }}
                @if($submission->form && $submission->form->description)
                    &middot; {{ $submission->form->description }}
                @endif
            </div>
            @if($parentCase)
                <div class="rs-form-desc mt-1">
                    <i class="mdi mdi-subdirectory-arrow-right me-1"></i>Follows up on
                    <a href="{{ route('form.records.show', $parentCase->id) }}" class="fw-semibold">
                        {{ $parentCase->recordReference() }} —
                        {{ app(\App\Services\FormRecordService::class)->titleFor($parentCase) }}
                    </a>
                </div>
            @endif
        </div>
        <div class="d-flex align-items-center gap-3">
            {{-- Offered on completed and closed cases too: a finished job that
                 needs a return visit is exactly what this is for. --}}
            @if($canFollowUp)
                <a href="{{ route('form.fill', ['id' => $root->form_id, 'parent' => $root->id]) }}"
                   class="btn btn-primary">
                    <i class="mdi mdi-plus me-1"></i> Start follow-up case
                </a>
            @endif
            {{-- Closing and reopening belong to whoever opened the case. --}}
            @if($canClose)
                @if($root->isRecordOpen())
                    <button type="button" class="btn btn-outline-secondary"
                            data-bs-toggle="modal" data-bs-target="#closeRecordModal">
                        <i class="mdi mdi-lock-outline me-1"></i>Close
                    </button>
                @else
                    <form action="{{ route('form.records.reopen', $root->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary">
                            <i class="mdi mdi-lock-open-variant-outline me-1"></i>Reopen
                        </button>
                    </form>
                @endif
            @endif
            <div class="rs-submitter">
                <div class="rs-avatar">{{ strtoupper(substr($submission->submittedBy->name ?? 'U', 0, 1)) }}</div>
                <div>
                    <div class="rs-submitter-name">{{ $submission->submittedBy->name ?? 'Unknown' }}</div>
                    <div class="rs-submitter-date">Submitted {{ $submission->created_at->format('d M Y, h:i A') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Why this record was closed. Closing cancels whatever was in flight,
         so the reason has to be visible to everyone who lands here later. --}}
    @unless($root->isRecordOpen())
        <div class="rs-closed-card">
            <div class="rs-closed-head">
                <i class="mdi mdi-lock-outline"></i>This record is closed
            </div>
            @if($root->record_closed_remark)
                <div class="rs-closed-remark">{{ $root->record_closed_remark }}</div>
            @endif
            @if($root->record_closed_at)
                <div class="rs-closed-meta">
                    Closed by {{ optional($root->recordClosedBy)->name ?? 'an administrator' }}
                    on {{ $root->record_closed_at->format('d M Y, h:i A') }}.
                </div>
            @endif
        </div>
    @endunless

    {{-- Return visits opened from this case. Only shown when there are any, so
         a case that never came back never sees this block. Each is a case in
         its own right — its own reference, process and status. --}}
    @if($childCases->isNotEmpty())
        <div class="rs-card mb-3">
            <div class="rs-card-head">
                <div class="rs-card-title">Follow-up cases</div>
                <span class="rs-card-meta">{{ $childCases->count() }} opened from this one</span>
            </div>
            <div class="rs-card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover form-table align-middle mb-0">
                        <tbody>
                            @foreach($childCases as $child)
                                <tr>
                                    <td style="width:110px;" class="text-muted small fw-semibold">
                                        {{ $child->recordReference() }}
                                    </td>
                                    <td>
                                        {{ app(\App\Services\FormRecordService::class)->titleFor($child) }}
                                        <div class="text-muted" style="font-size:0.75rem;">
                                            {{ $child->created_at->format('d M Y, h:i A') }}
                                            &middot; {{ optional($child->submittedBy)->name ?? 'Unknown' }}
                                        </div>
                                    </td>
                                    <td class="text-center" style="width:130px;">
                                        @include('page.form._status-badge', ['status' => $child->status])
                                    </td>
                                    <td class="text-center" style="width:110px;">
                                        <a href="{{ route('form.records.show', $child->id) }}"
                                           class="btn btn-sm btn-outline-primary">Open</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <div class="rs-grid">

        {{-- LEFT: responses + fill --}}
        <div class="rs-left">

            <div class="rs-card">
                <div class="rs-card-head">
                    <div class="rs-card-title">Responses</div>
                    <div class="rs-card-meta">
                        {{ $responseFieldCount }} field{{ $responseFieldCount === 1 ? '' : 's' }}
                        @if($responseSectionCount)
                            &middot; {{ $responseSectionCount }} section{{ $responseSectionCount === 1 ? '' : 's' }}
                        @endif
                    </div>
                </div>
                <div class="rs-card-body">
                    @forelse($responseSections as $section)
                        <div class="rs-section">
                            @if($section['label'])
                                <div class="rs-section-head">
                                    <div class="rs-section-label">{{ $section['label'] }}</div>
                                </div>
                            @endif
                            <div class="rs-fields-grid">
                                @foreach($section['fields'] as $field)
                                    @include('page.form.partials._response-field', ['field' => $field])
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="rs-empty">
                            <i class="mdi mdi-file-document-outline"></i>
                            <p class="mb-0">No field data visible for this submission.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- What earlier handlers filled before the process looped back here.
                 Sits above the fill card so it is read before the work starts. --}}
            @include('page.form.partials._previous-rounds', ['previousRounds' => $previousRounds])

            {{-- Fill phase: this viewer's editable section, rendered with the
                 real form design (groups, ordering) and auto-scrolled into view --}}
            @if($fillStage)
            @php $requiredCount = collect($fillElements)->where('mandatory', true)->count(); @endphp
            <div class="rs-card" id="fill-section-card">
                <div class="rs-card-head">
                    <div>
                        <div class="rs-card-title">
                            <span style="display:inline-block;width:8px;height:8px;border-radius:999px;background:#7a56d1;margin-right:9px;"></span>{{ $fillStage->name }} — your section
                        </div>
                        <div class="rs-card-meta mt-1">
                            {{ count($fillElements) }} field{{ count($fillElements) === 1 ? '' : 's' }}{{ $requiredCount ? ' · ' . $requiredCount . ' required' : '' }}
                        </div>
                    </div>
                    <span class="rs-fill-badge">Awaiting your input</span>
                </div>
                <div class="rs-card-body py-4">
                    @if($errors->any())
                        <div class="alert alert-danger py-2 px-3 small">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="fill-section-form" action="{{ route('form.admin.fill', $submission->id) }}" method="POST"
                          enctype="multipart/form-data" novalidate>
                        @csrf
                        <input type="hidden" name="form_data" id="fill_section_json">

                        <div class="rs-fill-grid">
                            @include('page.form.partials._form-render', [
                                'tree'            => $fillTree,
                                'answers'         => $fillAnswers,
                                'visibility'      => [],
                                'form'            => $submission->form,
                                'disabled'        => false,
                                // Last round's answer as a hint under each input.
                                'previousAnswers' => $fillPrevious,
                            ])
                        </div>

                        <div class="text-center pt-2 border-top mt-2">
                            <button type="submit" class="btn text-white mt-3" style="background:#7a56d1;">
                                <i class="mdi mdi-check me-1"></i>Submit My Section
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- No history widget here: "Entries in this record" above already
                 answers "what came before", and duplicating it was confusing. --}}
            @endif

        </div>

        {{-- RIGHT: sidebar --}}
        <div class="rs-right">

            {{-- Approval progress --}}
            <div class="rs-card rs-sidebar-card">
                <div class="rs-sidebar-title">Approval Progress</div>
                @include('page.form.partials._review-timeline', [
                    'submission' => $submission, 'canApprove' => $canApprove, 'waitingFor' => $waitingFor,
                ])
            </div>

            {{-- Actions --}}
            @php
                $currentStage = $submission->currentStage();
                $approvalRows = $submission->approvals->filter(fn ($r) => $r->isBlockingNode())->values();
                $curIdx       = $currentStage ? $approvalRows->search(fn ($r) => $r->id === $currentStage->id) : false;
                $nextRow      = $curIdx !== false ? $approvalRows->get($curIdx + 1) : null;
            @endphp
            @if($canApprove && !$fillStage)
            <div class="rs-card rs-sidebar-card">
                <div class="rs-sidebar-title">{{ $currentStage ? $currentStage->name : 'Actions' }}</div>
                <div class="rs-actions">
                    <button type="button" class="btn btn-success w-100" id="btn-approve">
                        <i class="mdi mdi-check-circle-outline me-1"></i>Approve Submission
                    </button>
                    <button type="button" class="btn btn-outline-danger w-100" id="btn-reject">
                        <i class="mdi mdi-close-circle-outline me-1"></i>Reject Submission
                    </button>
                </div>
                <div class="rs-actions-help">
                    @if($nextRow)
                        Approving advances the submission to <strong>{{ $nextRow->name }}</strong>.
                    @else
                        Approving completes the approval process.
                    @endif
                </div>
            </div>
            @elseif($waitingFor->isNotEmpty())
                @php
                    $waitingNames = $waitingFor->pluck('name');
                    $waitingText  = $waitingNames->count() > 1
                        ? $waitingNames->slice(0, -1)->implode(', ') . ' and ' . $waitingNames->last()
                        : $waitingNames->first();
                @endphp
                <div class="rs-waiting-card">
                    <strong>Waiting on this stage.</strong>
                    <span>{{ $waitingText }} still {{ $waitingNames->count() > 1 ? 'need' : 'needs' }} to act before you can review.</span>
                </div>
            @endif

            {{-- The submitter's own controls over this entry. They used to live
                 on the old My Submissions list; the record page is now the only
                 place an entry is opened, so they belong here. --}}
            @php
                $isMine     = (int) $submission->submitted_by === (int) auth()->id();
                $canEdit    = $isMine && $submission->status === 'pending' && $submission->isUntouched();
                // Cancelling withdraws your own case while it is still pending.
                // Admins have Close, which does the same but demands a reason.
                $canCancel  = $isMine && $submission->status === 'pending';
                $canClone   = $isMine && optional($submission->form)->is_enabled;
            @endphp
            @if($canEdit || $canCancel || $canClone)
                <div class="rs-card rs-sidebar-card">
                    <div class="rs-sidebar-title">This entry</div>
                    <div class="rs-actions">
                        @if($canEdit)
                            <a href="{{ route('form.submission.edit', $submission->id) }}" class="btn btn-outline-primary w-100">
                                <i class="mdi mdi-pencil-outline me-1"></i>Edit answers
                            </a>
                        @endif
                        @if($canClone)
                            <a href="{{ route('form.submission.clone', $submission->id) }}" class="btn btn-outline-secondary w-100">
                                <i class="mdi mdi-content-copy me-1"></i>Start a new record from this
                            </a>
                        @endif
                        @if($canCancel)
                            <button type="button" class="btn btn-outline-danger w-100"
                                    data-bs-toggle="modal" data-bs-target="#cancelEntryModal">
                                <i class="mdi mdi-cancel me-1"></i>Cancel entry
                            </button>
                        @endif
                    </div>
                    @unless($canEdit)
                        <div class="rs-actions-help">
                            Answers can no longer be edited once someone has acted on this entry.
                        </div>
                    @endunless
                </div>
            @endif

        </div>
    </div>
</div>

{{-- Cancel Entry Modal --}}
@if($canCancel)
<div class="modal fade" id="cancelEntryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rs-modal">
            <form action="{{ route('form.submission.cancel', $submission->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="rs-modal-icon rs-modal-icon--danger"><i class="mdi mdi-cancel"></i></div>
                    <div class="rs-modal-title">Cancel this submission?</div>
                    <div class="rs-modal-desc">
                        <strong>{{ $rsSubId }}</strong> will be withdrawn and removed from
                        {{ $waitingFor->isNotEmpty() ? 'the queue of whoever is reviewing it' : 'any pending queue' }}.
                        This cannot be undone — you would have to submit the form again.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Keep it</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="mdi mdi-cancel me-1"></i>Yes, cancel it
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Close Record Modal --}}
@if($canClose && $root->isRecordOpen())
<div class="modal fade" id="closeRecordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rs-modal">
            <form action="{{ route('form.records.close', $root->id) }}" method="POST" id="closeRecordForm">
                @csrf
                <div class="modal-body">
                    <div class="rs-modal-icon rs-modal-icon--danger"><i class="mdi mdi-lock-outline"></i></div>
                    <div class="rs-modal-title">Close this record?</div>
                    <div class="rs-modal-desc">
                        <strong>{{ $recordTitle }}</strong> ({{ $root->recordReference() }}) will be marked closed.
                        @if($submission->status === 'pending')
                            <div class="alert alert-warning py-2 px-3 small mt-3 mb-0">
                                This case is <strong>still in progress</strong> and will be
                                <strong>cancelled</strong>. Whoever is holding it will lose the task.
                            </div>
                        @endif
                        <div class="text-muted small mt-3">
                            A closed case can still be followed up on later.
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label small fw-semibold">Reason for closing <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="remark" id="closeRecordRemark" rows="3"
                                  placeholder="Why is this record being closed?"></textarea>
                        <div class="invalid-feedback">A reason is required to close a record.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" id="btn-confirm-close">
                        <i class="mdi mdi-lock-outline me-1"></i>Yes, Close Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Approve Confirmation Modal --}}
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rs-modal">
            <form action="{{ route('form.admin.approve', $submission->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="rs-modal-icon rs-modal-icon--success"><i class="mdi mdi-check"></i></div>
                    <div class="rs-modal-title">Approve submission?</div>
                    <div class="rs-modal-desc">
                        You're approving <strong>{{ $rsSubId }}</strong> from
                        <strong>{{ $submission->submittedBy->name ?? 'this user' }}</strong>
                        @if($currentStage)
                            for the <strong>{{ $currentStage->name }}</strong> stage
                        @endif.
                    </div>
                    <div class="mt-3">
                        <label class="form-label small fw-semibold">Remark <span class="text-muted fw-normal">(optional)</span></label>
                        <textarea class="form-control" name="remark" rows="3" placeholder="Add a remark..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="mdi mdi-check me-1"></i>Yes, Approve
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('page.form.partials._image-lightbox')

{{-- Reject Confirmation Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rs-modal">
            <form action="{{ route('form.admin.reject', $submission->id) }}" method="POST" id="rejectForm">
                @csrf
                <div class="modal-body">
                    <div class="rs-modal-icon rs-modal-icon--danger"><i class="mdi mdi-close"></i></div>
                    <div class="rs-modal-title">Reject submission?</div>
                    <div class="rs-modal-desc">
                        The submitter will be notified with your reason. This ends the current approval flow for
                        <strong>{{ $rsSubId }}</strong>.
                    </div>
                    <div class="mt-3">
                        <label class="form-label small fw-semibold">Reason for rejection <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="remark" id="rejectRemark" rows="3"
                                  placeholder="State the reason for rejection..."></textarea>
                        <div class="invalid-feedback">A rejection remark is required.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" id="btn-confirm-reject">
                        Yes, Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('page.form.partials._assignee-picker')
@endsection

@section('script')
{{-- The handler's fill card evaluates visible_when live, exactly as the
     fill page does. Without this the card showed every conditional field
     regardless of its condition, and never revealed one the handler's own
     answers had just satisfied. --}}
<script src="{{ asset('js/forms/form-conditions.js') }}"></script>
<script>
    $(document).ready(function () {
        @if($fillStage)
        // Bring the section that needs filling into view and focus its first input.
        setTimeout(function () {
            var $card = $('#fill-section-card');
            if (!$card.length) return;
            $('html, body').animate({ scrollTop: $card.offset().top - 80 }, 450, function () {
                $card.addClass('fill-attn');
                $card.find('input:visible, select:visible, textarea:visible')
                     .not('[type="hidden"], [type="checkbox"], [type="radio"], [type="file"]')
                     .first().trigger('focus');
            });
        }, 250);

        (function () {
            const fillElements = @json($fillElements);
            const fullSchema   = @json($schema);
            // Conditions can reference fields answered in an earlier stage, so
            // start from the saved answers and layer the card's live values on.
            const savedAnswers = @json($fillAnswers ?? new stdClass);

            function collectAllAnswers() {
                const answers = Object.assign({}, savedAnswers);
                fillElements.forEach(function (el) { answers[el.id] = readValue(el); });
                return answers;
            }

            function applyFillVisibility() {
                if (typeof FormConditions === 'undefined') return;
                const visibility = FormConditions.resolveVisibility(fullSchema, collectAllAnswers());
                $('#fill-section-card .form-element-wrapper').each(function () {
                    const id = $(this).data('el-id');
                    $(this).toggleClass('d-none', visibility[id] === false);
                });
            }
            const fileUrls = {};
            fillElements.forEach(function (el) {
                if (el.type === 'file') {
                    const current = $(`#fill-section-card .file-input[data-el-id="${el.id}"]`).data('current');
                    if (current) fileUrls[el.id] = current;
                }
            });

                        // Files post with the form as files[el_id][], the way the IFE and Task
            // pages do it. Nothing leaves the browser until submit, so this only
            // reports what is queued and flags anything over the size limit.
            $(document).on('change', '.file-input', function () {
                const elId  = $(this).data('el-id');
                const files = Array.from(this.files || []);
                const $out  = $(`#upload_status_${elId}`);

                $(`#error_${elId}`).text('').removeClass('d-block');

                if (!files.length) { $out.html(''); return; }

                const tooBig = files.filter(f => f.size > 10240 * 1024);
                const list   = files.map(f =>
                    `<span class="badge rounded-pill px-3 py-2 me-1 mb-1" style="background:#e8eeff;color:#3a5bd9;font-size:0.8rem;">`
                    + `<i class="mdi mdi-paperclip me-1"></i>${$('<div>').text(f.name).html()}</span>`).join('');

                $out.html(`<div class="mt-1">${list}</div>`
                    + (tooBig.length
                        ? `<div class="text-danger small mt-1"><i class="mdi mdi-alert-circle me-1"></i>`
                          + `${tooBig.length} file(s) are over the 10 MB limit and will be rejected.</div>`
                        : ''));
            });

            function readValue(el) {
                const $scope = $('#fill-section-card');
                switch (el.type) {
                    case 'multi-choice': {
                        const vals = [];
                        $scope.find(`.multi-choice-input[data-parent="${el.id}"]:checked`).each(function () { vals.push($(this).val()); });
                        return vals;
                    }
                    case 'multi-select': {
                        const vals = [];
                        $scope.find(`.multi-select-input[data-parent="${el.id}"]:checked`).each(function () { vals.push($(this).val()); });
                        return vals;
                    }
                    case 'checkbox': {
                        const vals = [];
                        $scope.find(`.checkbox-input[data-parent="${el.id}"]:checked`).each(function () { vals.push($(this).val()); });
                        return vals;
                    }
                case 'file': {
                    const $input = $scope.find(`.file-input[data-el-id="${el.id}"]`);
                    const picked = ($input[0] && $input[0].files) ? $input[0].files.length : 0;
                    if (picked > 0) return '__files_attached__';
                    // Untouched: send back what is already there, or the answer
                    // would be blanked on save.
                    const current = $input.attr('data-current');
                    return current ? JSON.parse(current) : null;
                }
                    case 'gps': {
                        // {lat, lng, accuracy, captured_at}, written by form-gps.js.
                        const raw = $scope.find(`.gps-input[data-el-id="${el.id}"]`).val();
                        try { return raw ? JSON.parse(raw) : null; } catch (e) { return null; }
                    }
                    default:
                        return $scope.find(`.form-input[data-el-id="${el.id}"]`).val() ?? null;
                }
            }

            // Re-evaluate as the handler answers, and once on load so a field
            // whose condition is already false never flashes into view.
            $('#fill-section-card').on('change input',
                '.form-input, .multi-choice-input, .multi-select-input, .checkbox-input, .file-input, .gps-input',
                applyFillVisibility);
            applyFillVisibility();

            $('#fill-section-form').on('submit', function (e) {
                e.preventDefault();
                let valid = true;
                const data = [];
                fillElements.forEach(function (el) {
                    const value = readValue(el);
                    const empty = value === null || value === '' || (Array.isArray(value) && value.length === 0);
                    const $error = $(`#error_${el.id}`);
                    $error.text('').removeClass('d-block');
                    if ((el.mandatory ?? false) && empty) {
                        $error.text('This field is required.').addClass('d-block');
                        valid = false;
                    }
                    data.push({ id: el.id, value: value });
                });
                if (!valid) return;
                $('#fill_section_json').val(JSON.stringify(data));
                this.submit();
            });
        })();
        @endif

        $('#btn-approve').on('click', function () {
            $('#approveModal').modal('show');
        });
        $('#btn-reject').on('click', function () {
            $('#rejectRemark').val('').removeClass('is-invalid');
            $('#rejectModal').modal('show');
        });
        $('#rejectForm').on('submit', function (e) {
            const remark = $('#rejectRemark').val().trim();
            if (!remark) {
                e.preventDefault();
                $('#rejectRemark').addClass('is-invalid').focus();
            }
        });
        $('#rejectRemark').on('input', function () {
            $(this).removeClass('is-invalid');
        });

        @if($canClose)
        // Closing cancels a live case, so the reason is mandatory here too.
        // Only shipped when the modal is — otherwise this binds to nothing.
        $('#closeRecordModal').on('show.bs.modal', function () {
            $('#closeRecordRemark').val('').removeClass('is-invalid');
        });
        $('#closeRecordForm').on('submit', function (e) {
            if (!$('#closeRecordRemark').val().trim()) {
                e.preventDefault();
                $('#closeRecordRemark').addClass('is-invalid').focus();
            }
        });
        @endif
        $('#closeRecordRemark').on('input', function () {
            $(this).removeClass('is-invalid');
        });
    });
</script>
@stack('lightbox-script')

@endsection
