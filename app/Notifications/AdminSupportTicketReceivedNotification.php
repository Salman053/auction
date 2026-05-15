<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminSupportTicketReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public SupportTicket $ticket
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function queue(): string
    {
        return 'high';
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Inquiry Received: '.$this->ticket->subject)
            ->greeting('Admin Hub Alert,')
            ->line('A new direct inquiry has been submitted.')
            ->line('Requester: '.$this->ticket->requester_name.' ('.$this->ticket->requester_email.')')
            ->line('Type: '.$this->ticket->subject)
            ->action('View Ticket', route('admin.support-tickets.show', $this->ticket))
            ->line('Please respond within the SLA window.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'subject' => $this->ticket->subject,
            'requester' => $this->ticket->requester_name,
            'message' => 'New support inquiry from ' . ($this->ticket->requester_name ?? $this->ticket->requester_email),
        ];
    }
}
