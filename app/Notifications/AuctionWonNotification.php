<?php

namespace App\Notifications;

use App\Models\Auction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AuctionWonNotification extends Notification
{
    use Queueable;

    public function __construct(public Auction $auction, public int $amountYen) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Congratulations! You won an auction on WatchHub')
            ->greeting("Hello {$notifiable->name}!")
            ->line("You have successfully won the auction for: **{$this->auction->title}**.")
            ->line('Winning Bid: **¥'.number_format($this->amountYen).'**')
            ->line('Shipping Destination: **'.($this->auction->winner->shippingRate?->name ?? 'Default').'**')
            ->line('Shipping Fee: **¥'.number_format($this->auction->winner->shippingRate?->fee_yen ?? 4000).'**')
            ->action('Confirm Shipment Details', route('user.auctions.show', $this->auction))
            ->line('Please confirm your shipping details by clicking the button above so our team can begin the authentication and delivery process.')
            ->line('Thank you for choosing WatchHub – The Destination for Luxury Horology.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'auction_id' => $this->auction->id,
            'auction_title' => $this->auction->title,
            'amount_yen' => $this->amountYen,
            'message' => "Congratulations! You won: {$this->auction->title}",
        ];
    }
}
