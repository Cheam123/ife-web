{{--
    Shared chrome for the public legal pages. Extends the nav-free layout so
    these render for signed-out visitors (and for the Play Console's crawler).
--}}
@extends('layouts.master-without-nav')

@section('content')

<style>
    .legal-page { background-color: #f6f6f9; min-height: 100vh; padding: 40px 0 64px; }
    .legal-wrap { max-width: 840px; margin: 0 auto; padding: 0 16px; }
    .legal-brand { display: block; height: 34px; margin-bottom: 28px; }
    .legal-card { background: #fff; border-radius: 10px; padding: 40px; box-shadow: 0 1px 3px rgba(0,0,0,.06); }
    .legal-card h1 { font-size: 28px; font-weight: 700; margin-bottom: 6px; }
    .legal-card h2 { font-size: 18px; font-weight: 600; margin-top: 34px; margin-bottom: 12px; }
    .legal-card h3 { font-size: 15px; font-weight: 600; margin-top: 22px; margin-bottom: 8px; }
    .legal-card p, .legal-card li { font-size: 14.5px; line-height: 1.7; color: #343a40; }
    .legal-card ul { padding-left: 20px; }
    .legal-card li { margin-bottom: 6px; }
    .legal-effective { font-size: 13px; color: #74788d; margin-bottom: 0; }
    .legal-table { width: 100%; font-size: 14px; margin-top: 8px; }
    .legal-table th { font-weight: 600; background: #f8f9fa; }
    .legal-table th, .legal-table td { padding: 10px 12px; border: 1px solid #eff0f2; vertical-align: top; }
    .legal-callout { background: #f8f9fa; border-left: 3px solid #556ee6; padding: 14px 18px; margin: 18px 0; border-radius: 0 6px 6px 0; }
    .legal-callout p:last-child { margin-bottom: 0; }
    .legal-footer { margin-top: 28px; text-align: center; font-size: 13px; color: #74788d; }
    .legal-footer a { color: #556ee6; }
    .legal-footer span { margin: 0 8px; color: #ced4da; }
    @media (max-width: 575px) { .legal-card { padding: 24px 20px; } }
</style>

<div class="legal-page">
    <div class="legal-wrap">

        <img src="{{ URL::asset('assets/brand/ronda-logo.svg') }}" alt="Ronda" class="legal-brand">

        <div class="legal-card">
            <h1>@yield('legal-heading')</h1>
            <p class="legal-effective">Last updated: 28 September 2026</p>

            @yield('legal-content')
        </div>

        <div class="legal-footer">
            <a href="{{ route('legal.privacy') }}">Privacy Policy</a>
            <span>&middot;</span>
            <a href="{{ route('legal.terms') }}">Terms of Service</a>
            <span>&middot;</span>
            <a href="{{ route('legal.accountDeletion') }}">Delete My Account</a>
        </div>

    </div>
</div>

@endsection
