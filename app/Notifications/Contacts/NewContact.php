<?php

namespace App\Notifications\Contacts;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewContact extends Notification
{
    use Queueable;

    protected $contact;
    /**
     * Create a new notification instance.
     */
    public function __construct($contact)
    {
        $this->contact = $contact;
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
        ->subject('New Contact')
        ->line('An Visitor has requested a new contact.')
        ->action('Show Contact', url('/admins/contact-us/show?id=' . encrypt($this->contact->id)))
        ->line('Waiting for Contact!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable): array
    {
        return [
            'title' => 'New Contact',
            'details' => 'An Visitor has requested a new contact.',
            'link' => '/admins/contact-us/show?id=' . encrypt($this->contact->id),
            'icon' => 'fas fa-envelope',
        ];
    }
}
