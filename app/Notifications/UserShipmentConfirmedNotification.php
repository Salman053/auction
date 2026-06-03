<?php

namespace App\Notifications;

use App\Models\Auction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserShipmentConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Auction $auction;
    public User $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Auction $auction, User $user)
    {
        $this->auction = $auction;
        $this->user = $user;
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
        $subject = "Shipment Confirmed by User: Auction #{$this->auction->yahoo_auction_id} - {$this->auction->title}";
        $message = (new MailMessage)
            ->subject($subject)
            ->greeting('Hello Admin,')
            ->line("The winner ({$this->user->name}, User ID: {$this->user->id}) has confirmed shipment details for the following auction:")
            ->line("Auction ID: #{$this->auction->yahoo_auction_id}")
            ->line("Title: {$this->auction->title}")
            ->action('View Auction', url('/admin/auctions/'.$this->auction->id))
            ->line('Please review and approve the shipment.');

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
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'status' => 'bidder_confirmed',
        ];
    }
}
