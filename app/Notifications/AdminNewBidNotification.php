<?php

namespace App\Notifications;

use App\Models\Auction;
use App\Models\Bid;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminNewBidNotification extends Notification
{
    use Queueable;

    public function __construct(public Auction $auction, public Bid $bid, public User $user) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Bid Received: ' . $this->auction->title)
            ->greeting("Hello Admin,")
            ->line("A new bid has been placed on the platform.")
            ->line("User: {$this->user->name} ({$this->user->email})")
            ->line("Auction: {$this->auction->title}")
            ->line("Yahoo ID: {$this->auction->yahoo_auction_id}")
            ->line("Bid Amount: ¥" . number_format($this->bid->amount_yen))
            ->line("Max Bid: ¥" . number_format($this->bid->max_amount_yen))
            ->action('View Auction in Admin', route('admin.auctions.show', $this->auction))
            ->line('System auto-notification.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'auction_id' => $this->auction->id,
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'bid_amount' => $this->bid->amount_yen,
            'message' => "New bid of ¥" . number_format($this->bid->amount_yen) . " by {$this->user->name} on {$this->auction->title}",
        ];
    }
}
