@extends('legal.layout')

@section('title', 'Delete My Account')
@section('legal-heading', 'Delete My Account')

@section('legal-content')

    <p>Use this page to ask us to delete your Ronda account and the personal data held with it. You do not need the app installed to submit a request.</p>

    @if (session('deletion_submitted'))

        <div class="alert alert-success" role="alert" style="margin-top: 24px;">
            <h3 style="margin-top: 0;">Your request has been received</h3>
            <p style="margin-bottom: 0;">If the address you entered matches a Ronda account, our team will verify the request and erase the personal data held with it within 30 days. We will contact you at that address if we need anything further.</p>
        </div>

    @else

        @if (session('deletion_error'))
            <div class="alert alert-danger" role="alert" style="margin-top: 24px;">
                {{ session('deletion_error') }}
            </div>
        @endif

        <h2>What happens when you submit this</h2>

        <ul>
            <li>Our team is notified and verifies that the request genuinely comes from you.</li>
            <li>Your account is disabled, so you can no longer sign in.</li>
            <li>Within 30 days we erase your personal data: your name, email address, mobile number, uploaded photos and documents, and your push notification token.</li>
            <li>Records of work you performed — tasks, outlets and orders, visit reports and form submissions, including their Location Stamps — are kept as your employer's business records, with your identifying details removed.</li>
        </ul>

        <div class="legal-callout">
            <p><strong>Ronda accounts are created and owned by your employer.</strong> We may need to confirm your request with them before erasing data, for example where records must be retained under your employment terms or by law.</p>
        </div>

        <p>If you have the app installed, you can also do this from <strong>More &rarr; Account &rarr; Delete Account</strong>, which disables your account immediately.</p>

        <h2>Request deletion</h2>

        <form method="POST" action="{{ route('legal.accountDeletion.submit') }}" style="margin-top: 16px;">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email address on your Ronda account</label>
                <input
                    type="email"
                    class="form-control @error('email') is-invalid @enderror"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    maxlength="255"
                    autocomplete="email">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="reason" class="form-label">Reason <span class="text-muted">(optional)</span></label>
                <textarea
                    class="form-control @error('reason') is-invalid @enderror"
                    id="reason"
                    name="reason"
                    rows="3"
                    maxlength="1000">{{ old('reason') }}</textarea>
                @error('reason')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-danger">Request account deletion</button>
        </form>

        <p style="margin-top: 24px; font-size: 13px; color: #74788d;">
            You can also email us directly at
            <a href="mailto:{{ config('mail.deletion_notify_address') }}">{{ config('mail.deletion_notify_address') }}</a>.
            See our <a href="{{ route('legal.privacy') }}">Privacy Policy</a> for what we collect and how long we keep it.
        </p>

    @endif

@endsection
