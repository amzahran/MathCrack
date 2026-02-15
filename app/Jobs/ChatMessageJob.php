<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Chat;
use App\Models\User;
use App\Models\ChatMessage;
use App\Notifications\Chats\NewChatMessage;
use App\Notifications\Chats\NewGroupMessage;

class ChatMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $chat;
    public $message;
    public $sender;

    /**
     * Create a new job instance.
     */
    public function __construct(Chat $chat, ChatMessage $message, User $sender)
    {
        $this->chat = $chat;
        $this->message = $message;
        $this->sender = $sender;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // استثناء المرسل من الإشعارات
        $recipients = $this->chat->users()->where('users.id', '!=', $this->sender->id)->get();

        foreach ($recipients as $recipient) {
            if ($this->chat->is_group) {
                $recipient->notify(new NewGroupMessage($this->chat, $this->message, $this->sender));
            } else {
                $recipient->notify(new NewChatMessage($this->chat, $this->message, $this->sender));
            }
        }
    }
}