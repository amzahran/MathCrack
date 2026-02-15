<?php

namespace App\Notifications\Chats;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Chat;
use App\Models\User;

class AddedToGroupChat extends Notification implements ShouldQueue
{
    use Queueable;

    protected $chat;
    protected $addedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct(Chat $chat, User $addedBy)
    {
        $this->chat = $chat;
        $this->addedBy = $addedBy;
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
        $url = route('dashboard.admins.chats.show', ['id' => encrypt($this->chat->id)]);
        return (new MailMessage)
            ->subject('You have been added to a new group chat: ' . $this->chat->name)
            ->line($this->addedBy->firstname . ' ' . $this->addedBy->lastname . ' added you to a new group chat: ' . $this->chat->name)
            ->action('View the chat', $url)
            ->line('Thank you for using our app!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $url = route('dashboard.admins.chats.show', ['id' => encrypt($this->chat->id)]);
        return [
            'title' => 'New Group Chat',
            'details' => $this->addedBy->firstname . ' ' . $this->addedBy->lastname . ' added you to a new group chat: ' . $this->chat->name,
            'link' => $url,
            'icon' => 'fas fa-comments',
        ];
    }
}