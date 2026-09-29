@extends('layouts.master')
@section('title') @lang('translation.home') @endsection

@section('content')
@php
    $now        = $overview['generated_at'];
    $attention  = $overview['attention'];
    $status     = $overview['status'];
    $pulse      = $overview['pulse'];
    $people     = $overview['people'];
    $setup      = $overview['setup'];
    $currency   = config('ife.currency', 'RM');
    $hour       = (int) $now->format('G');
    $greeting   = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
    $firstName  = Str::before(trim($user->name), ' ');
    $statusIcon = ['ok' => 'check-circle', 'warning' => 'alert', 'critical' => 'error', 'neutral' => 'clock', 'info' => 'gauge'];
    $levelText  = ['critical' => 'Urgent', 'warning' => 'Needs a check', 'info' => 'To do'];
    $deltaIcon  = ['up' => 'arrow-up', 'down' => 'arrow-down', 'flat' => 'minus'];
    $icon       = fn (string $name, int $size = 20) => view('page.partials.dash-icon', ['name' => $name, 'size' => $size]);
@endphp

@include('page.partials.dashboard-styles')

<div class="adm">

    <div class="adm-head">
        <div>
            <h1 class="adm-title">{{ $greeting }}, {{ $firstName }}</h1>
            <p class="adm-meta">{{ $now->format('l, j M Y') }} · Updated {{ $now->format('g:i A') }}</p>
        </div>
        <div class="adm-head-actions">
            @include('page.partials.dashboard-tabs', ['active' => 'overview'])
            <a href="{{ url('index') }}" class="adm-btn">{{ $icon('refresh', 18) }} Refresh</a>
        </div>
    </div>

    <div class="adm-stack">

        <div class="adm-row-2">

            {{-- Needs your attention: only rows with something to do, most urgent first --}}
            <section class="adm-card" aria-labelledby="adm-attn-title">
                <h2 id="adm-attn-title" class="adm-card-title">
                    Needs your attention
                    @if($attention['total'] > 0)
                        <span class="adm-count">{{ $attention['total'] }}</span>
                    @endif
                </h2>

                @if(empty($attention['items']))
                    <div class="adm-empty">
                        <div class="adm-empty-icon">{{ $icon('check', 28) }}</div>
                        <p class="adm-empty-title">All set. Nothing needs your attention today.</p>
                        <p class="adm-card-sub">Last checked {{ $now->format('g:i A') }}.</p>
                        <a href="{{ url('index') }}?tab=team" class="adm-btn mt-3">Open team dashboard {{ $icon('arrow-right', 16) }}</a>
                    </div>
                @else
                    <p class="adm-card-sub">Only items that need an action are shown. Most urgent first.</p>
                    <ul class="adm-list">
                        @foreach($attention['items'] as $item)
                            <li class="adm-item">
                                <span class="adm-badge adm-badge--{{ $item['level'] }}">{{ $icon($item['icon']) }}</span>
                                <div class="adm-item-body">
                                    <div class="adm-item-title"><span class="visually-hidden">{{ $levelText[$item['level']] }}: </span>{{ $item['title'] }}</div>
                                    <div class="adm-item-detail">{{ $item['detail'] }}</div>
                                </div>
                                <a href="{{ $item['url'] }}" class="adm-btn">{{ $item['action'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                    @if($attention['more'] > 0)
                        <p class="adm-note mt-2 mb-0">{{ $attention['more'] }} more {{ $attention['more'] === 1 ? 'item is' : 'items are' }} in the cards below.</p>
                    @endif
                @endif
            </section>

            {{-- System status: every line has an icon, a word and a time, never colour alone --}}
            <section id="adm-status" class="adm-card adm-status" aria-labelledby="adm-status-title">
                <h2 id="adm-status-title" class="adm-card-title">System status</h2>
                <p class="adm-card-sub">{{ $status['summary'] }}</p>
                <ul class="adm-list">
                    @foreach($status['lines'] as $line)
                        <li class="adm-item">
                            <span class="adm-status-icon adm-status-icon--{{ $line['level'] }}">{{ $icon($statusIcon[$line['level']]) }}</span>
                            <div class="adm-item-body">
                                <div class="adm-item-title">{{ $line['label'] }}</div>
                                <div class="adm-item-detail">{{ $line['detail'] }}</div>
                                @if($line['link'])
                                    <a href="{{ $line['link']['url'] }}" class="adm-link adm-link--tight">{{ $line['link']['text'] }}</a>
                                @endif
                            </div>
                            @if($line['chip'])
                                <span class="adm-chip adm-chip--{{ $line['level'] }}">{{ $line['chip'] }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
                <p class="adm-note adm-status-foot">Morning Round-Up runs daily at 7:00 AM. Suggested Orders refresh at 2:45 AM. Focus Alert check runs every hour.</p>
            </section>
        </div>

        {{-- Headline numbers: four only --}}
        <div class="adm-row-4">
            <section class="adm-card adm-kpi" aria-labelledby="adm-kpi-people">
                <div class="adm-kpi-head">
                    <h3 id="adm-kpi-people" class="adm-kpi-label">Active people</h3>
                    <button type="button" class="adm-info" data-bs-toggle="tooltip" title="People with an active account who signed in within the last {{ \App\Services\AdminDashboardService::QUIET_DAYS }} days." aria-label="About active people">{{ $icon('info', 16) }}</button>
                </div>
                <div class="adm-kpi-value">{{ number_format($pulse['people']['signed_in']) }} <small>of {{ number_format($pulse['people']['active']) }}</small></div>
                <div class="adm-bar adm-bar--thin mt-2" role="progressbar" aria-labelledby="adm-kpi-people" aria-valuenow="{{ $pulse['people']['share'] }}" aria-valuemin="0" aria-valuemax="100"><span style="width: {{ $pulse['people']['share'] }}%"></span></div>
                <div class="adm-kpi-note mt-2">signed in within {{ \App\Services\AdminDashboardService::QUIET_DAYS }} days</div>
            </section>

            <section class="adm-card adm-kpi" aria-labelledby="adm-kpi-visits">
                <div class="adm-kpi-head">
                    <h3 id="adm-kpi-visits" class="adm-kpi-label">Visits, 7 days</h3>
                    <button type="button" class="adm-info" data-bs-toggle="tooltip" title="Visit reports filed today and in the 6 days before." aria-label="About visits">{{ $icon('info', 16) }}</button>
                </div>
                <div class="adm-kpi-value">{{ number_format($pulse['visits']['value']) }}</div>
                <div class="adm-delta adm-delta--{{ $pulse['visits']['change']['direction'] }}">{{ $icon($deltaIcon[$pulse['visits']['change']['direction']], 16) }} {{ $pulse['visits']['change']['text'] }}</div>
            </section>

            <section class="adm-card adm-kpi" aria-labelledby="adm-kpi-orders">
                <div class="adm-kpi-head">
                    <h3 id="adm-kpi-orders" class="adm-kpi-label">Orders this month</h3>
                    <button type="button" class="adm-info" data-bs-toggle="tooltip" title="Confirmed orders from {{ $now->copy()->startOfMonth()->format('j M Y') }} to today. Cancelled orders are not counted." aria-label="About orders">{{ $icon('info', 16) }}</button>
                </div>
                <div class="adm-kpi-value adm-kpi-value--money">{{ $currency }} {{ number_format($pulse['orders']['value'], 2) }}</div>
                <div class="adm-delta adm-delta--{{ $pulse['orders']['change']['direction'] }}">{{ $icon($deltaIcon[$pulse['orders']['change']['direction']], 16) }} {{ $pulse['orders']['change']['text'] }}</div>
                <div class="adm-kpi-note">{{ number_format($pulse['orders']['count']) }} {{ Str::plural('order', $pulse['orders']['count']) }}</div>
            </section>

            <section class="adm-card adm-kpi" aria-labelledby="adm-kpi-focus">
                <div class="adm-kpi-head">
                    <h3 id="adm-kpi-focus" class="adm-kpi-label">Focus Alerts</h3>
                    <button type="button" class="adm-info" data-bs-toggle="tooltip" title="At risk: due within {{ (int) config('ife.risk.hours') }} h, or {{ (int) round(config('ife.risk.elapsed') * 100) }}% of the time used. Overdue: past the due time." aria-label="About Focus Alerts">{{ $icon('info', 16) }}</button>
                </div>
                <div class="adm-focus-pair">
                    <div>
                        <span class="adm-focus-num">{{ number_format($pulse['focus']['at_risk']) }}</span>
                        <span class="adm-focus-label" style="color: var(--warn);">{{ $icon('alert', 14) }} at risk</span>
                    </div>
                    <div>
                        <span class="adm-focus-num">{{ number_format($pulse['focus']['overdue']) }}</span>
                        <span class="adm-focus-label" style="color: var(--crit);">{{ $icon('error', 14) }} overdue</span>
                    </div>
                </div>
                <a href="{{ url('index') }}?tab=team" class="adm-link">Open team dashboard {{ $icon('arrow-right', 16) }}</a>
            </section>
        </div>

        {{-- People: sign-ins and app setup. Never locations. --}}
        <section class="adm-card" aria-labelledby="adm-people-title">
            <h2 id="adm-people-title" class="adm-card-title">People</h2>
            <p class="adm-card-sub">Sign-ins and app setup. Ronda never shows where people are.</p>

            <div class="adm-tools">
                <div class="adm-search">
                    <label for="adm-people-search" class="visually-hidden">Search people</label>
                    {{ $icon('search') }}
                    <input id="adm-people-search" type="search" class="adm-input" placeholder="Search by name" autocomplete="off">
                </div>
                <div class="adm-filters" role="group" aria-label="Filter by role">
                    @foreach(['all' => 'All', 'rep' => 'Field reps', 'manager' => 'Managers', 'admin' => 'Admins'] as $key => $label)
                        <button type="button" class="adm-filter" data-role="{{ $key }}" aria-pressed="{{ $key === 'all' ? 'true' : 'false' }}">{{ $label }} <span>{{ $people['counts'][$key] }}</span></button>
                    @endforeach
                </div>
                <div class="adm-sort">
                    <label for="adm-people-sort">Sort by</label>
                    <select id="adm-people-sort" class="adm-select">
                        <option value="rank">Needs attention first</option>
                        <option value="name">Name (A to Z)</option>
                        <option value="seen">Last sign-in (oldest first)</option>
                        <option value="visits">Visits (most first)</option>
                    </select>
                </div>
            </div>

            <table class="adm-table adm-table--stack" id="adm-people-table">
                <caption class="visually-hidden">People with their last sign-in, app setup and visits</caption>
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Role</th>
                        <th scope="col">Team</th>
                        <th scope="col">Last sign-in</th>
                        <th scope="col">Mobile app</th>
                        <th scope="col" class="num">Visits, 7 days</th>
                        <th scope="col">Account</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($people['rows'] as $index => $person)
                        <tr data-name="{{ Str::lower($person['name']) }}"
                            data-role="{{ $person['role_key'] }}"
                            data-rank="{{ $index }}"
                            data-seen="{{ $person['last_login'] ? $person['minutes'] : 999999999 }}"
                            data-visits="{{ $person['visits_7d'] ?? -1 }}"
                            @if($index >= $people['shown']) hidden @endif>
                            <td>
                                <div class="adm-person">
                                    <span class="adm-avatar" aria-hidden="true">{{ $person['initials'] }}</span>
                                    <a href="{{ route('users.view', $person['id']) }}">{{ $person['name'] }}</a>
                                </div>
                            </td>
                            <td data-label="Role">{{ $person['role'] }}</td>
                            <td data-label="Team">{{ $person['team'] }}</td>
                            <td data-label="Last sign-in">
                                @if($person['quiet'])
                                    <span class="adm-quiet" title="{{ $person['seen_exact'] }}">{{ $icon('clock', 16) }} {{ $person['seen'] }}</span>
                                @else
                                    <span title="{{ $person['seen_exact'] }}">{{ $person['seen'] }}</span>
                                @endif
                            </td>
                            <td data-label="Mobile app">
                                @if($person['app_ready'])
                                    <span class="adm-chip adm-chip--ok adm-chip--sm">{{ $icon('check', 14) }} Ready</span>
                                @elseif($person['no_app'])
                                    <span class="adm-chip adm-chip--warning adm-chip--sm" title="Not signed in to the Ronda app on a phone, or notifications are off">{{ $icon('alert', 14) }} Not set up</span>
                                @else
                                    <span class="adm-muted">Not set up</span>
                                @endif
                            </td>
                            <td data-label="Visits, 7 days" class="num">{{ $person['visits_7d'] ?? '—' }}</td>
                            <td data-label="Account">
                                @if($person['active'])
                                    Active
                                @else
                                    <span class="adm-chip adm-chip--inactive adm-chip--sm">Inactive</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    <tr class="adm-no-match" @if($people['total'] > 0) hidden @endif>
                        <td colspan="7" class="adm-muted">{{ $people['total'] > 0 ? 'No one matches this search. Try another name or role.' : 'No people yet.' }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="adm-foot">
                <span class="adm-note" id="adm-people-count" aria-live="polite">Showing {{ min($people['shown'], $people['total']) }} of {{ $people['total'] }} {{ $people['total'] === 1 ? 'person' : 'people' }}</span>
                <a href="{{ route('users.index') }}" class="adm-btn">View all people {{ $icon('arrow-right', 16) }}</a>
            </div>
        </section>

        <div class="adm-row-3">

            {{-- Data health: gaps that stop features from working well --}}
            <section class="adm-card" aria-labelledby="adm-health-title">
                <h2 id="adm-health-title" class="adm-card-title">Data health</h2>
                <p class="adm-card-sub">Gaps here stop Ronda features from working well.</p>
                <ul class="adm-list">
                    @foreach($overview['health']['items'] as $i => $item)
                        <li class="adm-health-item">
                            <div class="adm-health-head">
                                <span id="adm-health-{{ $i }}" class="adm-health-label">{{ $item['label'] }}</span>
                                <span class="adm-chip adm-chip--sm adm-chip--{{ $item['status']['key'] }}">
                                    @if(in_array($item['status']['key'], ['good', 'done'], true))
                                        {{ $icon('check', 12) }}
                                    @elseif($item['status']['key'] === 'low')
                                        {{ $icon('alert', 12) }}
                                    @endif
                                    {{ $item['status']['text'] }}
                                </span>
                                <span class="adm-health-share">{{ $item['status']['key'] === 'none' ? '—' : $item['share'] . '%' }}</span>
                            </div>
                            <div class="adm-bar" role="progressbar" aria-labelledby="adm-health-{{ $i }}" aria-valuenow="{{ $item['share'] }}" aria-valuemin="0" aria-valuemax="100"><span style="width: {{ $item['share'] }}%"></span></div>
                            @if($item['note'])
                                <div class="adm-health-note">{{ $item['note'] }}</div>
                            @endif
                            <div class="adm-health-foot">
                                <span class="adm-muted">{{ $item['count'] }}</span>
                                @if($item['fix'])
                                    <a href="{{ $item['fix']['url'] }}">{{ $item['fix']['text'] }}</a>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>

            {{-- Coverage: a bar per area (position on a shared scale), with a table view --}}
            <section class="adm-card" aria-labelledby="adm-cov-title">
                <div class="adm-card-head">
                    <div>
                        <h2 id="adm-cov-title" class="adm-card-title">Outlet coverage by area</h2>
                        <p class="adm-card-sub">Share of outlets visited in the last 30 days</p>
                    </div>
                    @if(!empty($overview['coverage']))
                        <div class="adm-toggle" role="group" aria-label="Show coverage as">
                            <button type="button" data-view="chart" aria-pressed="true">Chart</button>
                            <button type="button" data-view="table" aria-pressed="false">Table</button>
                        </div>
                    @endif
                </div>

                @if(empty($overview['coverage']))
                    <p class="adm-card-sub mt-3">No areas yet. Add areas so outlets can be grouped.</p>
                    <a href="{{ route('area.index') }}" class="adm-link">Add an area {{ $icon('arrow-right', 16) }}</a>
                @else
                    <div data-coverage="chart">
                        <ul class="adm-cov" aria-label="Coverage by area">
                            @foreach($overview['coverage'] as $area)
                                <li>
                                    <div class="adm-cov-name">
                                        <strong>{{ $area['name'] }}</strong>
                                        <span>{{ $area['visited'] }} of {{ $area['outlets'] }} {{ Str::plural('outlet', $area['outlets']) }}</span>
                                    </div>
                                    <div class="adm-bar adm-bar--thick" aria-hidden="true"><span style="width: {{ $area['share'] }}%"></span></div>
                                    <div class="adm-cov-share">{{ $area['outlets'] > 0 ? $area['share'] . '%' : '—' }}</div>
                                </li>
                            @endforeach
                        </ul>
                        <div class="adm-cov-scale" aria-hidden="true"><span>0%</span><span>50%</span><span>100%</span></div>
                    </div>
                    <div data-coverage="table" hidden>
                        <table class="adm-table">
                            <caption class="visually-hidden">Outlets visited in the last 30 days, by area</caption>
                            <thead>
                                <tr>
                                    <th scope="col">Area</th>
                                    <th scope="col" class="num">Outlets</th>
                                    <th scope="col" class="num">Visited</th>
                                    <th scope="col" class="num">Share</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($overview['coverage'] as $area)
                                    <tr>
                                        <td><strong>{{ $area['name'] }}</strong></td>
                                        <td class="num">{{ $area['outlets'] }}</td>
                                        <td class="num">{{ $area['visited'] }}</td>
                                        <td class="num">{{ $area['outlets'] > 0 ? $area['share'] . '%' : '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            {{-- Setup at a glance + quick actions --}}
            <section class="adm-card" aria-labelledby="adm-setup-title">
                <h2 id="adm-setup-title" class="adm-card-title">Setup at a glance</h2>
                <p class="adm-card-sub">The people, forms, products and areas you look after.</p>
                <ul class="adm-list">
                    <li class="adm-setup-row">
                        <span class="adm-setup-icon">{{ $icon('users', 18) }}</span>
                        <div class="adm-item-body">
                            <div class="adm-setup-name">{{ number_format($setup['people']['total']) }} {{ Str::plural('person', $setup['people']['total']) }}</div>
                            <div class="adm-item-detail">{{ $setup['people']['detail'] }}</div>
                        </div>
                        <a href="{{ route('users.index') }}">Manage<span class="visually-hidden"> people</span></a>
                    </li>
                    <li class="adm-setup-row">
                        <span class="adm-setup-icon">{{ $icon('file-text', 18) }}</span>
                        <div class="adm-item-body">
                            <div class="adm-setup-name">{{ number_format($setup['forms']['total']) }} {{ Str::plural('form', $setup['forms']['total']) }}</div>
                            <div class="adm-item-detail">{{ $setup['forms']['enabled'] }} enabled · {{ $setup['forms']['total'] - $setup['forms']['enabled'] }} turned off</div>
                        </div>
                        <a href="{{ route('form.index') }}">Manage<span class="visually-hidden"> forms</span></a>
                    </li>
                    <li class="adm-setup-row">
                        <span class="adm-setup-icon">{{ $icon('package', 18) }}</span>
                        <div class="adm-item-body">
                            <div class="adm-setup-name">{{ number_format($setup['products']['active']) }} active {{ Str::plural('product', $setup['products']['active']) }}</div>
                            <div class="adm-item-detail">{{ $setup['products']['categories'] }} {{ Str::plural('category', $setup['products']['categories']) }} · {{ $setup['products']['inactive'] }} inactive</div>
                        </div>
                        <a href="{{ route('product.index') }}">Manage<span class="visually-hidden"> products</span></a>
                    </li>
                    <li class="adm-setup-row">
                        <span class="adm-setup-icon">{{ $icon('map', 18) }}</span>
                        <div class="adm-item-body">
                            <div class="adm-setup-name">{{ number_format($setup['areas']['total']) }} {{ Str::plural('area', $setup['areas']['total']) }}</div>
                            <div class="adm-item-detail">{{ number_format($setup['areas']['outlets']) }} {{ Str::plural('outlet', $setup['areas']['outlets']) }} in total</div>
                        </div>
                        <a href="{{ route('area.index') }}">Manage<span class="visually-hidden"> areas</span></a>
                    </li>
                </ul>
                <h3 class="adm-quick-title">Quick actions</h3>
                <div class="adm-quick">
                    <a href="{{ route('users.create') }}">{{ $icon('plus', 18) }} Add person</a>
                    <a href="{{ route('form.create') }}">{{ $icon('plus', 18) }} Create form</a>
                    <a href="{{ route('product.create') }}">{{ $icon('plus', 18) }} Add product</a>
                    <a href="{{ route('area.index') }}">{{ $icon('plus', 18) }} Add area</a>
                </div>
            </section>
        </div>

    </div>
</div>
@endsection

@section('script')
<script>
    (function () {
        // Metric definitions on the info buttons: hover and keyboard focus.
        if (window.bootstrap && bootstrap.Tooltip) {
            document.querySelectorAll('.adm [data-bs-toggle="tooltip"]').forEach(function (el) {
                new bootstrap.Tooltip(el);
            });
        }

        // People: search, role filter and sort in the page; shows the first matches.
        var table = document.getElementById('adm-people-table');
        if (table) {
            var body    = table.tBodies[0];
            var rows    = Array.prototype.slice.call(body.querySelectorAll('tr[data-role]'));
            var noMatch = body.querySelector('.adm-no-match');
            var search  = document.getElementById('adm-people-search');
            var sort    = document.getElementById('adm-people-sort');
            var count   = document.getElementById('adm-people-count');
            var filters = Array.prototype.slice.call(document.querySelectorAll('.adm-filter'));
            var limit   = {{ (int) $people['shown'] }};
            var role    = 'all';
            var sorters = {
                rank   : function (a, b) { return a.dataset.rank - b.dataset.rank; },
                name   : function (a, b) { return a.dataset.name.localeCompare(b.dataset.name); },
                seen   : function (a, b) { return b.dataset.seen - a.dataset.seen; },
                visits : function (a, b) { return b.dataset.visits - a.dataset.visits; }
            };

            var render = function () {
                var query   = search.value.trim().toLowerCase();
                var matches = rows.filter(function (row) {
                    return (role === 'all' || row.dataset.role === role)
                        && (!query || row.dataset.name.indexOf(query) !== -1);
                }).sort(sorters[sort.value] || sorters.rank);

                rows.forEach(function (row) { row.hidden = true; });
                matches.forEach(function (row, i) {
                    body.insertBefore(row, noMatch);
                    row.hidden = i >= limit;
                });
                noMatch.hidden    = matches.length > 0;
                count.textContent = 'Showing ' + Math.min(matches.length, limit) + ' of ' + matches.length + (matches.length === 1 ? ' person' : ' people');
            };

            search.addEventListener('input', render);
            sort.addEventListener('change', render);
            filters.forEach(function (button) {
                button.addEventListener('click', function () {
                    role = button.dataset.role;
                    filters.forEach(function (other) { other.setAttribute('aria-pressed', other === button ? 'true' : 'false'); });
                    render();
                });
            });
        }

        // Coverage: chart or table.
        var toggles = Array.prototype.slice.call(document.querySelectorAll('.adm-toggle [data-view]'));
        toggles.forEach(function (button) {
            button.addEventListener('click', function () {
                toggles.forEach(function (other) { other.setAttribute('aria-pressed', other === button ? 'true' : 'false'); });
                document.querySelectorAll('[data-coverage]').forEach(function (panel) {
                    panel.hidden = panel.dataset.coverage !== button.dataset.view;
                });
            });
        });
    })();
</script>
@endsection
