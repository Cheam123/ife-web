<?php

namespace App\Http\Controllers\MobileApp;

use App\Http\Controllers\Controller;
use App\Notifications\AccountDeletionRequested;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Throwable;

class AccountController extends Controller
{
    /**
     * Handle an in-app account deletion request.
     *
     * Disables the account and revokes its sessions; no personal data is
     * touched. The actual erasure is done by hand, prompted by the email this
     * sends — see App\Notifications\AccountDeletionRequested.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function requestDeletion(Request $request)
    {
        try {
            $validated = $request->validate([
                'reason' => 'nullable|string|max:1000',
            ]);

            $user = $request->user();

            // status is what mobileLogin checks (Auth::attempt([... 'status' => 1])),
            // so flipping it is what actually locks the account out.
            $user->status = 0;
            $user->save();

            // Blocking login is not enough on its own: auth:sanctum resolves a
            // user straight from the token and never looks at status, so the
            // token already on the device would keep working. Done last, since
            // $request->user() is resolved by the time we get here.
            $user->tokens()->delete();

            Notification::route('mail', config('mail.deletion_notify_address'))
                ->notify(new AccountDeletionRequested(
                    $user->email,
                    'app',
                    $validated['reason'] ?? null,
                    $user
                ));

            return $this->response_success([], 'Your account deletion request has been received.');

        } catch (Throwable $e) {
            \Log::error('Account deletion request failed', [
                'user_id' => optional($request->user())->id,
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return $this->response_failed('Failed to submit your deletion request. Please try again later.');
        }
    }
}
