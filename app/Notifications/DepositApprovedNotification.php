<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DepositApprovedNotification extends Notification
{
    use Queueable;

    public function __construct(public int $amountYen, public ?string $memo = null) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Wallet Deposit Approved!')
            ->line('Your deposit of ¥'.number_format($this->amountYen).' has been approved.')
            ->line($this->memo ? "Note: {$this->memo}" : '')
            ->action('View Wallet', route('user.wallet.index'))
            ->line('Your bidding capacity has been updated accordingly.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'deposit_approved',
            'amount_yen' => $this->amountYen,
            'message' => 'Your deposit of ¥'.number_format($this->amountYen).' has been approved.',
        ];
    }
}
