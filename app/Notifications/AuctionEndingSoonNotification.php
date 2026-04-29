<?php

namespace App\Notifications;

use App\Models\Auction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AuctionEndingSoonNotification extends Notification
{
    use Queueable;

    public function __construct(public Auction $auction) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Auction Ending Soon: ' . $this->auction->title)
            ->greeting("Hello {$notifiable->name},")
            ->line("An auction you are bidding on or watching is ending soon!")
            ->line("**{$this->auction->title}**")
            ->line("Ends at: " . ($this->auction->ends_at?->format('M d, H:i') ?? 'N/A'))
            ->action('Place a Final Bid', route('user.auctions.show', $this->auction))
            ->line('Don\'t miss out on this luxury horology piece!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'auction_id' => $this->auction->id,
            'title' => $this->auction->title,
            'ends_at' => $this->auction->ends_at,
            'message' => "Auction ending soon: {$this->auction->title}",
        ];
    }
}
