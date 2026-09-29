@extends('layouts.master',['tmenu_part1' => ($tmenu_part1 ?? ''), 'tmenu_part2' => ($tmenu_part2 ?? ''), 'tmenu_part3' => ($tmenu_part3 ?? '')] )
@section('title') @lang('translation.home') @endsection

@section('content')
@php
    $currency = config('ife.currency', 'RM');
    $tasks    = $summary['tasks'];
    // Stat-tile values auto-compact: 1,284 / 12.9K / 4.2M.
    $compact  = function ($n) {
        $n = (float) $n;
        if ($n >= 1000000) return rtrim(rtrim(number_format($n / 1000000, 1), '0'), '.') . 'M';
        if ($n >= 10000)   return rtrim(rtrim(number_format($n / 1000, 1), '0'), '.') . 'K';
        return number_format($n);
    };
@endphp

@include('page.partials.dashboard-styles')

<div class="dash py-2">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h5 class="mb-0">{{ $isTeam ? 'Team dashboard' : 'My dashboard' }}</h5>
            <span class="dash-sub">Updated {{ \Illuminate\Support\Carbon::parse($summary['generated_at'])->format('j M Y, g:i A') }} &middot; "7 days" is today and the six days before</span>
        </div>
        {{-- Admins land on the Overview; this is its Team tab --}}
        @if($isAdmin ?? false)
            @include('page.partials.dashboard-tabs', ['active' => 'team'])
        @endif
    </div>

    {{-- KPI tiles --}}
    <div class="dash-tiles mb-3">
        <div class="dash-tile">
            <div class="dash-tile-label">Open tasks</div>
            <div class="dash-tile-value">{{ $compact($tasks['open']) }}</div>
            <div class="dash-tile-note">{{ $tasks['new'] }} new &middot; {{ $tasks['in_progress'] }} in progress</div>
        </div>
        <div class="dash-tile">
            <div class="dash-tile-label"><i class="mdi mdi-alert-octagon" style="color:var(--critical);"></i> Overdue</div>
            <div class="dash-tile-value">{{ $compact($tasks['overdue']) }}</div>
            <div class="dash-tile-note">past their due time</div>
        </div>
        <div class="dash-tile">
            <div class="dash-tile-label"><i class="mdi mdi-alert" style="color:var(--serious);"></i> At risk</div>
            <div class="dash-tile-value">{{ $compact($tasks['at_risk']) }}</div>
            <div class="dash-tile-note">due within {{ (int) config('ife.risk.hours') }} h or {{ (int) round(config('ife.risk.elapsed') * 100) }}% of time used</div>
        </div>
        <div class="dash-tile">
            <div class="dash-tile-label">Due today</div>
            <div class="dash-tile-value">{{ $compact($tasks['due_today']) }}</div>
            <div class="dash-tile-note">open tasks</div>
        </div>
        <div class="dash-tile">
            <div class="dash-tile-label">Tasks done, 7 days</div>
            <div class="dash-tile-value">{{ $compact($tasks['done_7d']) }}</div>
            <div class="dash-tile-note">{{ $tasks['created_7d'] }} created &middot; {{ $tasks['completed_7d'] }} completed</div>
        </div>
        <div class="dash-tile">
            <div class="dash-tile-label">Visits, 7 days</div>
            <div class="dash-tile-value">{{ $compact($summary['visits']['last_7d']) }}</div>
            <div class="dash-tile-note">{{ $summary['visits']['today'] }} today</div>
        </div>
        <div class="dash-tile">
            <div class="dash-tile-label">Forms submitted, 7 days</div>
            <div class="dash-tile-value">{{ $compact($summary['forms']['submitted_7d']) }}</div>
            <div class="dash-tile-note">{{ $summary['forms']['pending'] }} pending &middot; {{ $summary['forms']['approved'] }} approved &middot; {{ $summary['forms']['rejected'] }} rejected</div>
        </div>
        <div class="dash-tile">
            <div class="dash-tile-label">Orders this month</div>
            <div class="dash-tile-value">{{ $currency }} {{ $compact($summary['orders']['month_value']) }}</div>
            <div class="dash-tile-note">{{ $summary['orders']['month_count'] }} orders &middot; {{ $currency }} {{ number_format($summary['orders']['month_value'], 2) }}</div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        {{-- Trend: two series, so a legend; hover shows both values per day --}}
        <div class="{{ $isTeam ? 'col-xl-7' : 'col-12' }}">
            <div class="dash-card">
                <h6>Tasks created vs. done, last 14 days</h6>
                <div class="dash-chart-wrap"><canvas id="trend-chart" aria-label="Tasks created and done per day, last 14 days" role="img"></canvas></div>
                <details class="mt-2">
                    <summary class="dash-sub">Show as table</summary>
                    <table class="dash-table mt-1">
                        <thead><tr><th>Date</th><th class="num">Created</th><th class="num">Done</th></tr></thead>
                        <tbody>
                            @foreach($summary['trend'] as $day)
                                <tr>
                                    <td>{{ \Illuminate\Support\Carbon::parse($day['date'])->format('D j M') }}</td>
                                    <td class="num">{{ $day['created'] }}</td>
                                    <td class="num">{{ $day['done'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </details>
            </div>
        </div>

        {{-- Morning Round-Up: the AI daily brief (managers) --}}
        @if($isTeam)
        <div class="col-xl-5">
            <div class="dash-card">
                <div class="d-flex justify-content-between align-items-start">
                    <h6>Morning Round-Up</h6>
                    <form method="POST" action="{{ route('dashboard.digest') }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary py-0" title="Write today's Morning Round-Up again from the latest figures">
                            <i class="mdi mdi-refresh"></i> Regenerate
                        </button>
                    </form>
                </div>
                @if(session('digest_status'))
                    <div class="alert alert-info py-1 px-2 small mb-2">{{ session('digest_status') }}</div>
                @endif
                @if($digest)
                    <div class="dash-sub mb-1">
                        {{ $digest->digest_date->format('l, j M Y') }} &middot;
                        {{ $digest->source === 'bedrock' ? 'Written by Claude (Amazon Bedrock)' : 'Template summary (Bedrock not configured or unavailable)' }}
                    </div>
                    <div class="dash-digest">
                        @php $inList = false; @endphp
                        @foreach(preg_split('/\r\n|\r|\n/', $digest->content) as $line)
                            @php $line = trim($line); @endphp
                            @if(\Illuminate\Support\Str::startsWith($line, '- '))
                                @if(!$inList) <ul> @php $inList = true; @endphp @endif
                                <li>{{ \Illuminate\Support\Str::after($line, '- ') }}</li>
                            @else
                                @if($inList) </ul> @php $inList = false; @endphp @endif
                                @if($line === '')
                                @elseif(\Illuminate\Support\Str::endsWith($line, ':') && mb_strlen($line) <= 30)
                                    <div class="label">{{ $line }}</div>
                                @else
                                    <div>{{ $line }}</div>
                                @endif
                            @endif
                        @endforeach
                        @if($inList) </ul> @endif
                    </div>
                @else
                    <div class="dash-sub">No Morning Round-Up yet. It is written at 7:00 AM each day. Press Regenerate to write one now.</div>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- Focus alerts --}}
    <div class="dash-card mb-3">
        <h6>Focus alerts <span class="dash-sub">(overdue first, then at risk; soonest due first)</span></h6>
        @if(empty($summary['attention']))
            <div class="dash-sub">Nothing is overdue or at risk.</div>
        @else
            <div style="overflow-x:auto;">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Task</th>
                            <th>Outlet</th>
                            <th>Subscriber</th>
                            <th>Status</th>
                            <th>Due</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($summary['attention'] as $item)
                            <tr>
                                <td>
                                    @if($item['level'] === 'overdue')
                                        <span class="dash-level"><i class="mdi mdi-alert-octagon" style="color:var(--critical);"></i> Overdue</span>
                                    @else
                                        <span class="dash-level"><i class="mdi mdi-alert" style="color:var(--serious);"></i> At risk</span>
                                    @endif
                                </td>
                                <td>{{ $item['reference'] }} &middot; {{ $item['title'] }}</td>
                                <td>{{ $item['lead'] }}</td>
                                <td>{{ $item['subscriber'] }}</td>
                                <td>{{ $item['status'] }}</td>
                                <td style="white-space:nowrap;">
                                    {{ $item['due_at'] ? \Illuminate\Support\Carbon::parse($item['due_at'])->format('j M, g:i A') : '' }}
                                    <div class="dash-sub">{{ $item['reason'] }}</div>
                                </td>
                                <td><a href="{{ url('v1/task/manage/view/' . $item['id']) }}" title="Open task"><i class="mdi mdi-clipboard-outline" style="font-size:18px;"></i></a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Team activity (managers) --}}
    @if($isTeam)
    <div class="dash-card mb-3">
        <h6>Team activity <span class="dash-sub">(open work as subscriber; activity over the last 7 days)</span></h6>
        @if(empty($summary['team']))
            <div class="dash-sub">No active team members yet.</div>
        @else
            <div style="overflow-x:auto;">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Role</th>
                            <th class="num">Open</th>
                            <th class="num">Overdue</th>
                            <th class="num">At risk</th>
                            <th class="num">Done</th>
                            <th class="num">Visits</th>
                            <th class="num">Forms</th>
                            <th class="num">Orders ({{ $currency }})</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($summary['team'] as $person)
                            <tr>
                                <td>{{ $person['name'] }}</td>
                                <td>{{ $person['role'] }}</td>
                                <td class="num">{{ $person['open'] }}</td>
                                <td class="num">{{ $person['overdue'] }}</td>
                                <td class="num">{{ $person['at_risk'] }}</td>
                                <td class="num">{{ $person['done_7d'] }}</td>
                                <td class="num">{{ $person['visits_7d'] }}</td>
                                <td class="num">{{ $person['forms_7d'] }}</td>
                                <td class="num">{{ number_format($person['orders_7d'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @endif
</div>
@endsection

@section('script')
<script>
    (function () {
        var canvas = document.getElementById('trend-chart');
        if (!canvas || typeof Chart === 'undefined') return;

        var trend  = @json($summary['trend']);
        var labels = trend.map(function (d) {
            var dt = new Date(d.date + 'T00:00:00');
            return dt.toLocaleDateString(undefined, { day: 'numeric', month: 'short' });
        });
        var bar = { maxBarThickness: 24, borderRadius: 4, borderSkipped: 'start', categoryPercentage: 0.7, barPercentage: 0.85 };

        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    Object.assign({ label: 'Created', data: trend.map(function (d) { return d.created; }), backgroundColor: '#0D729E' }, bar),
                    Object.assign({ label: 'Done', data: trend.map(function (d) { return d.done; }), backgroundColor: '#D9822B' }, bar)
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'start',
                        labels: { color: '#3D525C', boxWidth: 10, boxHeight: 10, font: { size: 11 } }
                    },
                    tooltip: {
                        backgroundColor: '#ffffff',
                        titleColor: '#0E2A36',
                        bodyColor: '#3D525C',
                        borderColor: 'rgba(14,42,54,0.15)',
                        borderWidth: 1,
                        padding: 8,
                        boxWidth: 10,
                        boxHeight: 10
                    }
                },
                scales: {
                    x: { grid: { display: false, drawBorder: true, borderColor: '#C9C3B6' }, ticks: { color: '#5B6B72', font: { size: 11 } } },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#E6E1D6', drawBorder: false },
                        ticks: { color: '#5B6B72', precision: 0, font: { size: 11 } }
                    }
                }
            }
        });
    })();
</script>
@endsection
