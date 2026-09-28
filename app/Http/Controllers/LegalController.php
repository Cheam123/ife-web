<?php

namespace App\Http\Controllers;

use App\Notifications\AccountDeletionRequested;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Throwable;

/**
 * Public legal pages: the privacy policy, terms of service, and the
 * account deletion request form.
 *
 * These have to be reachable without an account — the Play Console checks the
 * privacy policy and data deletion URLs from outside the app — so this
 * controller deliberately declares no auth middleware.
 */
class LegalController extends Controller
{
    public function privacy()
    {
        return view('legal.privacy');
    }

    public function terms()
    {
        return view('legal.terms');
    }

    public function accountDeletion()
    {
        return view('legal.account-deletion');
    }

    /**
     * Take a deletion request from the public form.
     *
     * Deliberately does not look up, change or acknowledge the account. The
     * form is unauthenticated, so acting on it would let anyone disable a
     * colleague by typing their email, and confirming whether an address
     * matched would turn it into an account enumeration oracle. An admin
     * verifies the requester before doing anything.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitAccountDeletion(Request $request)
    {
        $validated = $request->validate([
            'email'  => 'required|email|max:255',
            'reason' => 'nullable|string|max:1000',
        ]);

        try {
            Notification::route('mail', config('mail.deletion_notify_address'))
                ->notify(new AccountDeletionRequested(
                    $validated['email'],
                    'web',
                    $validated['reason'] ?? null
                ));
        } catch (Throwable $e) {
            \Log::error('Account deletion web request failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withInput()->with(
                'deletion_error',
                'We could not submit your request. Please email ' . config('mail.deletion_notify_address') . ' directly.'
            );
        }

        return redirect()->route('legal.accountDeletion')->with(
            'deletion_submitted',
            true
        );
    }
}
