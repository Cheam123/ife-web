@extends('legal.layout')

@section('title', 'Terms of Service')
@section('legal-heading', 'Terms of Service')

@section('legal-content')

    <p>These terms govern your use of the Ronda mobile app and the Ronda web system at <strong>hq.ronda.asia</strong> ("the Service"), operated by Ronda ("we", "us"). By using the Service you agree to them.</p>

    <h2>1. Accounts</h2>

    <p>Ronda is licensed to businesses for their staff. You cannot register yourself — your employer creates your account, sets your role and permissions, and can suspend or remove it at any time. Your right to use the Service lasts only as long as your employer's agreement with us and your relationship with them.</p>

    <p>You are responsible for keeping your password confidential and for activity under your account. Tell your employer immediately if you think someone else has access to it.</p>

    <h2>2. Acceptable use</h2>

    <p>You agree not to:</p>

    <ul>
        <li>Use the Service for anything unlawful, or in breach of your employer's policies;</li>
        <li>Upload content you have no right to share, or that is unlawful, offensive or infringing;</li>
        <li>Falsify records, including Location Stamps, reports and form submissions, or attempt to spoof your location;</li>
        <li>Access data belonging to other users or organisations that you have not been granted access to;</li>
        <li>Attempt to breach, probe or disrupt the Service, or reverse-engineer, decompile or copy any part of it;</li>
        <li>Use automated means to extract data from the Service.</li>
    </ul>

    <h2>3. Your content</h2>

    <p>Content you submit — photos, documents, reports, form answers and comments — remains owned by you or your employer as your employment arrangement provides. You grant us the licence needed to host, process and display it in order to run the Service. We do not use it for any other purpose.</p>

    <h2>4. Location</h2>

    <p>The Service records your precise location only when you submit a form that includes a Location Stamp, or when you stamp an outlet's location. It does not record your location in the background or while the app is closed. By using the Service you acknowledge this. What is recorded, and how to control it, is set out in our <a href="{{ route('legal.privacy') }}">Privacy Policy</a>. Whether a form requires a Location Stamp is decided by your employer, not us.</p>

    <h2>5. Availability</h2>

    <p>We aim to keep the Service available, but we do not guarantee uninterrupted or error-free operation. We may suspend it for maintenance, updates or security. Features may change or be withdrawn.</p>

    <h2>6. Disclaimer and liability</h2>

    <p>The Service is provided "as is", without warranties of any kind to the extent the law allows. We are not liable for indirect, incidental or consequential loss, or for loss of profits, revenue or data. Nothing in these terms limits liability that cannot lawfully be limited.</p>

    <p>We are not responsible for how your employer configures the Service, what they require of you, or how they use the records it produces.</p>

    <h2>7. Ending your use</h2>

    <p>You may request deletion of your account at any time — in the app under <strong>More &rarr; Account &rarr; Delete Account</strong>, or at <a href="{{ route('legal.accountDeletion') }}">{{ route('legal.accountDeletion') }}</a>. Your employer may also suspend or close your account. We may suspend access where these terms are breached. Records of work you performed remain with your employer as described in the <a href="{{ route('legal.privacy') }}">Privacy Policy</a>.</p>

    <h2>8. Changes</h2>

    <p>We may update these terms. The date at the top of this page shows when they last changed, and continuing to use the Service after a change means you accept the revised terms.</p>

    <h2>9. Governing law</h2>

    <p>These terms are governed by the laws of Malaysia, and the courts of Malaysia have exclusive jurisdiction over any dispute arising from them.</p>

    <h2>10. Contact</h2>

    <p>Email <a href="mailto:{{ config('mail.deletion_notify_address') }}">{{ config('mail.deletion_notify_address') }}</a> with any questions about these terms.</p>

@endsection
