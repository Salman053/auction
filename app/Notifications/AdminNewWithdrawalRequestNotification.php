<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\WithdrawalRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminNewWithdrawalRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public WithdrawalRequest $withdrawal, public User $user) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function queue(): string
    {
        return 'high';
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Withdrawal Request: ¥'.number_format($this->withdrawal->amount_yen))
            ->greeting('Hello Admin,')
            ->line("A new withdrawal request has been submitted by {$this->user->name} ({$this->user->email}).")
            ->line('Amount: ¥'.number_format($this->withdrawal->amount_yen))
            ->line('Method: '.ucfirst(str_replace('_', ' ', $this->withdrawal->destination_type ?? 'N/A')))
            ->line('Details: '.($this->withdrawal->memo ?? 'No details provided'))
            ->action('Review Withdrawal', route('admin.withdrawals.index', ['status' => 'pending']))
            ->line('Please review and process this request in the admin dashboard.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'withdrawal_id' => $this->withdrawal->id,
            'user_name' => $this->user->name,
            'amount' => $this->withdrawal->amount_yen,
            'message' => 'New withdrawal request of ¥'.number_format($this->withdrawal->amount_yen)." from {$this->user->name}",
        ];
    }
}
