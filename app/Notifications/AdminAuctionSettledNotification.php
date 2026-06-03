<?php

namespace App\Notifications;

use App\Models\Auction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminAuctionSettledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Auction $auction;
    public ?User $winner;
    public string $status;
    public ?int $winningBidAmount;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Auction $auction, string $status, ?User $winner = null, ?int $winningBidAmount = null)
    {
        $this->auction = $auction;
        $this->status = $status;
        $this->winner = $winner;
        $this->winningBidAmount = $winningBidAmount;
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
        $subject = "Auction Settled: #{$this->auction->yahoo_auction_id} - {$this->auction->title}";
        $message = (new MailMessage)
            ->subject($subject)
            ->greeting('Hello Admin,')
            ->line("An auction has been settled with the following details:")
            ->line("Auction ID: #{$this->auction->yahoo_auction_id}")
            ->line("Title: {$this->auction->title}")
            ->line("Status: {$this->status}");

        if ($this->winner) {
            $message->line("Winner: {$this->winner->name} (User ID: {$this->winner->id})")
                ->line("Winning Bid Amount: ¥".number_format($this->winningBidAmount)." JPY");
        } elseif ($this->status === 'ended_outbid_on_yahoo') {
            $message->line("Reason: The highest internal bidder was outbid on Yahoo.");
        } elseif ($this->status === 'ended_no_bids') {
            $message->line("Reason: The auction ended with no bids.");
        }

        $message->action('View Auction', url('/admin/auctions/'.$this->auction->id))
            ->line('Thank you for using our application!');

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
            'status' => $this->status,
            'winner_id' => $this->winner->id ?? null,
            'winner_name' => $this->winner->name ?? null,
            'winning_bid_amount' => $this->winningBidAmount,
        ];
    }
}
