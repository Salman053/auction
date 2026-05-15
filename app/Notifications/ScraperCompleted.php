<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ScraperCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $totalCreated,
        public int $totalUpdated,
        public int $durationSeconds
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('✅ Scraper Completed Successfully')
                    ->greeting('Hello Admin,')
                    ->line('The Yahoo Auctions global scraper has successfully finished its run.')
                    ->line('**New Auctions Added:** ' . number_format($this->totalCreated))
                    ->line('**Auctions Updated:** ' . number_format($this->totalUpdated))
                    ->line('**Duration:** ' . $this->durationSeconds . ' seconds')
                    ->action('View Live Market', url('/auctions'))
                    ->line('The background queue is currently processing the detailed data sync for the new items.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'total_created' => $this->totalCreated,
            'total_updated' => $this->totalUpdated,
            'duration_seconds' => $this->durationSeconds,
            'message' => "Scraper finished: {$this->totalCreated} new, {$this->totalUpdated} updated.",
        ];
    }
}
