@extends('layouts.master')
@section('title') App errors @endsection

@section('content')
@include('page.partials.dashboard-styles')

<div class="adm">
    <div class="adm-head">
        <div>
            <h1 class="adm-title">App errors</h1>
            <p class="adm-meta">Errors the Ronda mobile app reported. What people typed is not shown here.</p>
        </div>
        <div class="adm-head-actions">
            <nav class="adm-tabs" aria-label="Time period">
                @foreach($windows as $key => $label)
                    <a href="{{ route('admin.app-errors', ['since' => $key]) }}" class="adm-tab" @if($since === $key) aria-current="page" @endif>{{ $label }}</a>
                @endforeach
            </nav>
            <a href="{{ url('index') }}" class="adm-btn">Back to overview</a>
        </div>
    </div>

    <div class="adm-stack">
        @if($screens->isNotEmpty())
            <section class="adm-card" aria-labelledby="err-screens">
                <h2 id="err-screens" class="adm-card-title">Screens with the most errors</h2>
                <ul class="adm-list">
                    @foreach($screens as $screen)
                        <li class="adm-item">
                            <div class="adm-item-body">
                                <div class="adm-item-title">{{ $screen->page_name ?: 'Screen not recorded' }}</div>
                            </div>
                            <span class="adm-chip adm-chip--neutral">{{ number_format($screen->total) }} {{ Str::plural('error', $screen->total) }}</span>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        <section class="adm-card" aria-labelledby="err-list">
            <h2 id="err-list" class="adm-card-title">
                All errors
                <span class="adm-count">{{ number_format($reports->total()) }}</span>
            </h2>

            @if($reports->isEmpty())
                <div class="adm-empty">
                    <div class="adm-empty-icon">@include('page.partials.dash-icon', ['name' => 'check', 'size' => 28])</div>
                    <p class="adm-empty-title">No app errors in this period.</p>
                    <p class="adm-card-sub">{{ $windows[$since] }}.</p>
                </div>
            @else
                <table class="adm-table">
                    <caption class="visually-hidden">App errors, newest first</caption>
                    <thead>
                        <tr>
                            <th scope="col">Time</th>
                            <th scope="col">Screen</th>
                            <th scope="col">Message</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $report)
                            <tr>
                                <td style="white-space: nowrap;">{{ $report->created_at->format('j M Y, g:i A') }}</td>
                                <td>{{ $report->page_name ?: '—' }}</td>
                                <td>
                                    @if(mb_strlen((string) $report->error_message) > 160)
                                        <details>
                                            <summary>{{ Str::limit($report->error_message, 160) }}</summary>
                                            <div class="adm-message">{{ $report->error_message }}</div>
                                        </details>
                                    @else
                                        {{ $report->error_message ?: '—' }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-3">{{ $reports->links() }}</div>
            @endif
        </section>
    </div>
</div>
@endsection
