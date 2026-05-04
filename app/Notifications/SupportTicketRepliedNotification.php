<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupportTicketRepliedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public SupportTicket $ticket,
        public SupportTicketMessage $message
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Update on your Inquiry: '.$this->ticket->subject)
            ->greeting('Hello '.($this->ticket->requester_name ?? 'User').',')
            ->line('Our administrative team has replied to your inquiry.')
            ->line('Response:')
            ->line('"'.$this->message->body.'"')
            ->action('View Details', route('user.support.show', $this->ticket))
            ->line('Thank you for using WatchHub.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'message_id' => $this->message->id,
            'subject' => $this->ticket->subject,
        ];
    }
}
