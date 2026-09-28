{{--
    "Earlier rounds" card — what previous handlers filled in before the process
    looped back to this phase.

    Those answers were archived into `round_snapshots` and blanked out of
    `form_elements` when the round restarted (FormApprovalService::startRound),
    so this card is the only place a later handler can read them.

    Params: $previousRounds = FormController::buildRoundSections()
            [['iteration','closed_at','actors','sections','field_count'], ...]

    Collapsed by default: the current round is the one being worked on, history
    is there when it is wanted. Newest round first.
--}}
@if(!empty($previousRounds))
    <div class="rs-card mb-3">
        <div class="rs-card-head">
            <div class="rs-card-title">
                <i class="mdi mdi-history me-1 text-muted"></i>Earlier rounds
            </div>
            <span class="rs-card-meta">
                {{ count($previousRounds) }} round{{ count($previousRounds) === 1 ? '' : 's' }} before this one
            </span>
        </div>

        <div class="rs-card-body pt-3">
            <div class="accordion accordion-flush" id="previousRoundsAccordion">
                @foreach($previousRounds as $i => $round)
                    @php $panelId = 'previous-round-' . $round['iteration']; @endphp

                    <div class="accordion-item border-0">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed px-0 py-3 shadow-none"
                                    style="background:transparent;font-size:13.5px;font-weight:600;"
                                    type="button" data-bs-toggle="collapse"
                                    data-bs-target="#{{ $panelId }}"
                                    aria-expanded="false" aria-controls="{{ $panelId }}">
                                <span class="rs-tag me-2">Round {{ $round['iteration'] }}</span>
                                <span class="text-muted fw-normal">
                                    {{ $round['actors'] ?: 'Handler' }}
                                    @if($round['closed_at'])
                                        &middot; {{ \Carbon\Carbon::parse($round['closed_at'])->format('d M Y H:i') }}
                                    @endif
                                    &middot; {{ $round['field_count'] }} field{{ $round['field_count'] === 1 ? '' : 's' }}
                                </span>
                            </button>
                        </h2>

                        <div id="{{ $panelId }}" class="accordion-collapse collapse"
                             data-bs-parent="#previousRoundsAccordion">
                            <div class="accordion-body px-0 pt-0">
                                @forelse($round['sections'] as $section)
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
                                    <div class="rs-empty py-3">
                                        <p class="mb-0">Nothing was recorded in this round.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif
