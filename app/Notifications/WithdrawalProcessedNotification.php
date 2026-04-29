<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WithdrawalProcessedNotification extends Notification
{
    use Queueable;

    public function __construct(public int $amountYen, public string $status, public ?string $memo = null) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = $this->status === 'approved' ? 'Withdrawal Approved' : 'Withdrawal Rejected';

        return (new MailMessage)
            ->subject($subject)
            ->line('Your withdrawal request of ¥'.number_format($this->amountYen)." has been {$this->status}.")
            ->line($this->memo ? "Note: {$this->memo}" : '')
            ->action('View Transactions', route('user.wallet.index'))
            ->line('Thank you for using our platform.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'withdrawal_processed',
            'status' => $this->status,
            'amount_yen' => $this->amountYen,
            'message' => 'Your withdrawal request of ¥'.number_format($this->amountYen)." has been {$this->status}.",
        ];
    }
}
