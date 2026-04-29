<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminNewDepositRequestNotification extends Notification
{
    use Queueable;

    public function __construct(public WalletTransaction $transaction, public User $user) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Deposit Request: ¥' . number_format($this->transaction->amount_yen))
            ->greeting("Hello Admin,")
            ->line("A new deposit request has been submitted by {$this->user->name} ({$this->user->email}).")
            ->line("Amount: ¥" . number_format($this->transaction->amount_yen))
            ->line("Provider: " . ucfirst($this->transaction->provider))
            ->action('Review Deposit', route('admin.deposits.index', ['status' => 'pending']))
            ->line('Please review and approve/reject this request in the admin dashboard.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'transaction_id' => $this->transaction->id,
            'user_name' => $this->user->name,
            'amount' => $this->transaction->amount_yen,
            'message' => "New deposit request of ¥" . number_format($this->transaction->amount_yen) . " from {$this->user->name}",
        ];
    }
}
