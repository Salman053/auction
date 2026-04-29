<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowBalanceNotification extends Notification
{
    use Queueable;

    public function __construct(public int $currentBalance, public int $threshold) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Low Wallet Balance Alert')
            ->greeting("Hello {$notifiable->name},")
            ->line("Your wallet balance is currently ¥" . number_format($this->currentBalance) . ".")
            ->line("This is below your threshold of ¥" . number_format($this->threshold) . ".")
            ->line('To ensure your active bids remain valid and you can participate in more auctions, please top up your wallet.')
            ->action('Deposit Funds', route('user.wallet.index'))
            ->line('Thank you for using our platform!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'balance' => $this->currentBalance,
            'threshold' => $this->threshold,
            'message' => "Your wallet balance is low (¥" . number_format($this->currentBalance) . ")",
        ];
    }
}
