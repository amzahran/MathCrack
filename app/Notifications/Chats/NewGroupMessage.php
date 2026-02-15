<?php

namespace App\Notifications\Chats;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use App\Models\Chat;
use App\Models\User;
use App\Models\ChatMessage;

class NewGroupMessage extends Notification implements ShouldQueue
{
    use Queueable;

    protected $chat;
    protected $message;
    protected $sender;

    /**
     * Create a new notification instance.
     */
    public function __construct(Chat $chat, ChatMessage $message, User $sender)
    {
        $this->chat = $chat;
        $this->message = $message;
        $this->sender = $sender;
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
            ->subject('New Message in Group ' . $this->chat->name)
            ->line($this->sender->firstname . ' ' . $this->sender->lastname . ' sent a new message in group ' . $this->chat->name)
            ->line($this->message->content)
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
            'title' => 'New Message in Group ' . $this->chat->name,
            'details' => $this->sender->firstname . ' ' . $this->sender->lastname . ': ' . Str::limit($this->message->content, 50),
            'link' => $url,
            'icon' => 'fas fa-comments',
        ];
    }
}