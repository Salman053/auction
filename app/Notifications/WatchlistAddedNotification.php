<?php

namespace App\Notifications;

use App\Models\Auction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WatchlistAddedNotification extends Notification
{
    use Queueable;

    public function __construct(public Auction $auction) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'auction_id' => $this->auction->id,
            'auction_title' => $this->auction->title,
            'message' => "You added {$this->auction->title} to your watchlist.",
        ];
    }
}
