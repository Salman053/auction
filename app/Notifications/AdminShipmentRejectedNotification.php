<?php

namespace App\Notifications;

use App\Models\Auction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminShipmentRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Auction $auction;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Auction $auction)
    {
        $this->auction = $auction;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $subject = "Shipment Status Update: Auction #{$this->auction->yahoo_auction_id} - {$this->auction->title}";
        $message = (new MailMessage)
            ->subject($subject)
            ->greeting("Hello {$notifiable->name},")
            ->line("The administrator has reviewed your shipment request for the following auction and reset its status:")
            ->line("Auction ID: #{$this->auction->yahoo_auction_id}")
            ->line("Title: {$this->auction->title}")
            ->action('View Auction', url('/user/auctions/'.$this->auction->id))
            ->line('Please review the auction details and re-confirm if necessary.');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'auction_id' => $this->auction->id,
            'yahoo_auction_id' => $this->auction->yahoo_auction_id,
            'title' => $this->auction->title,
            'status' => 'admin_rejected_or_reset',
        ];
    }
}
