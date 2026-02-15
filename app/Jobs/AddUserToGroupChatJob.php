<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Chat;
use App\Models\User;
use App\Notifications\Chats\AddedToGroupChat;

class AddUserToGroupChatJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $chat;
    public $userToAdd;
    public $addedBy;

    /**
     * Create a new job instance.
     */
    public function __construct(Chat $chat, User $userToAdd, User $addedBy)
    {
        $this->chat = $chat;
        $this->userToAdd = $userToAdd;
        $this->addedBy = $addedBy;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // إرسال إشعار للمستخدم بأنه تمت إضافته إلى المجموعة
        $this->userToAdd->notify(new AddedToGroupChat($this->chat, $this->addedBy));
    }
}