<?php

namespace App\Notifications;

use App\Models\Auction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OutbidNotification extends Notification
{
    use Queueable;

    public function __construct(public Auction $auction, public int $newBidAmount) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('You have been outbid!')
            ->line("You have been outbid on: {$this->auction->title}")
            ->line('The current bid is now ¥'.number_format($this->newBidAmount).'.')
            ->action('Place a Higher Bid', route('user.auctions.show', $this->auction))
            ->line('Thank you for using our platform!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'auction_id' => $this->auction->id,
            'title' => $this->auction->title,
            'new_bid_amount' => $this->newBidAmount,
            'message' => "You have been outbid on: {$this->auction->title}",
        ];
    }
}
