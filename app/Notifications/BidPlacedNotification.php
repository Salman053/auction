<?php

namespace App\Notifications;

use App\Models\Auction;
use App\Models\Bid;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BidPlacedNotification extends Notification
{
    use Queueable;

    public function __construct(public Auction $auction, public Bid $bid) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Bid Placed Successfully')
            ->greeting("Hello {$notifiable->name},")
            ->line("Your bid of ¥" . number_format($this->bid->amount_yen) . " has been successfully placed on:")
            ->line($this->auction->title)
            ->line("Your maximum bid limit is set to ¥" . number_format($this->bid->max_amount_yen) . ".")
            ->action('View Auction', route('user.auctions.show', $this->auction))
            ->line('We will notify you if you are outbid.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'auction_id' => $this->auction->id,
            'auction_title' => $this->auction->title,
            'bid_amount' => $this->bid->amount_yen,
            'message' => "You placed a bid of ¥" . number_format($this->bid->amount_yen) . " on {$this->auction->title}",
        ];
    }
}
