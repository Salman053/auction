<?php

namespace App\Notifications;

use App\Models\Auction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminShipmentApprovedNotification extends Notification implements ShouldQueue
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
        $subject = "Shipment Approved: Auction #{$this->auction->yahoo_auction_id} - {$this->auction->title}";
        $message = (new MailMessage)
            ->subject($subject)
            ->greeting("Hello {$notifiable->name},")
            ->line("Your shipment for the following auction has been approved by the administrator:")
            ->line("Auction ID: #{$this->auction->yahoo_auction_id}")
            ->line("Title: {$this->auction->title}")
            ->action('View Auction', url('/user/auctions/'.$this->auction->id))
            ->line('Please await further instructions regarding shipping.');

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
            'status' => 'admin_approved',
        ];
    }
}
