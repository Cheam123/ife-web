<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to the admin address in config('mail.deletion_notify_address') when a
 * user asks for their account to be deleted — either from the mobile app or
 * from the public /account-deletion form.
 *
 * Nothing about the request is stored, so this email is the whole audit trail.
 * The erasure itself is manual.
 */
class AccountDeletionRequested extends Notification
{
    use Queueable;

    public $email;
    public $source;
    public $reason;
    public $user;

    /**
     * @param  string       $email   The address the request was made for.
     * @param  string       $source  'app' (authenticated) or 'web' (public form).
     * @param  string|null  $reason  Optional free text from the requester.
     * @param  \App\Models\User|null  $user  Known only for app requests.
     * @return void
     */
    public function __construct($email, $source, $reason = null, $user = null)
    {
        $this->email  = $email;
        $this->source = $source;
        $this->reason = $reason;
        $this->user   = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $message = (new MailMessage)
            ->subject('Account deletion request — ' . config('app.name'))
            ->line('Someone has requested deletion of their ' . config('app.name') . ' account.')
            ->line('Email: ' . $this->email)
            ->line('Requested via: ' . ($this->source === 'app' ? 'the mobile app' : 'the account deletion web form'))
            ->line('Requested at: ' . now()->toDayDateTimeString());

        if ($this->user) {
            $message->line('User: ' . $this->user->name . ' (ID ' . $this->user->id . ')')
                    ->line('Their account has already been disabled and their app sessions revoked.');
        } else {
            // Web requests are unauthenticated, so nothing was changed — acting on
            // this one means verifying who sent it first.
            $message->line('This came from the public form, so no account has been changed. Verify the requester before acting.');
        }

        if ($this->reason) {
            $message->line('Reason given: ' . $this->reason);
        }

        return $message->line('Please erase this person\'s personal data within 30 days, as stated in the privacy policy.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'email'  => $this->email,
            'source' => $this->source,
            'reason' => $this->reason,
        ];
    }
}
