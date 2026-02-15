<?php

namespace App\Notifications\Tickets;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminRepliedToTicket extends Notification
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
        ->subject('Replied to Your Ticket')
        ->line('A support member has replied to your ticket.')
        ->action('Show Ticket', url('/users/tickets/show?id=' . encrypt($this->ticket->id)))
        ->line('Thank you for contacting us!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable): array
    {
        return [
            'title' => 'A support member has replied to your ticket',
            'link' => '/users/tickets/show?id=' . encrypt($this->ticket->id),
            'icon' => 'fas fa-ticket',
        ];
    }
}
