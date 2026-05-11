<?php

namespace App\Notifications;

use App\Models\Auction;
use App\Models\Bid;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WatchlistAuctionActivityNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Auction $auction,
        public Bid $bid
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'auction_id' => $this->auction->id,
            'auction_title' => $this->auction->title,
            'bid_amount' => $this->bid->amount_yen,
            'message' => "New bid of ¥" . number_format($this->bid->amount_yen) . " on watched item: {$this->auction->title}",
        ];
    }
}
