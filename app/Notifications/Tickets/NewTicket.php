<?php

namespace App\Notifications\Tickets;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTicket extends Notification
{
    use Queueable;

    protected $ticket;
    /**
     * Create a new notification instance.
     */
    public function __construct($ticket)
    {
        $this->ticket = $ticket;
    }

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
        ->subject('New Ticket')
        ->line('An user has created new ticket.')
        ->action('Show Ticket', url('/admins/tickets/show?id=' . encrypt($this->ticket->id)))
        ->line('Waiting for answer!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable): array
    {
        return [
            'title' => 'New Ticket',
            'details' => 'An user has created new ticket waiting for answer',
            'link' => '/admins/tickets/show?id=' . encrypt($this->ticket->id),
            'icon' => 'fas fa-ticket',
        ];
    }
}
