<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatParticipant extends Model
{
    protected $fillable = [
        'chat_id',
        'user_id',
        'is_admin',
        'last_read_at'
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'last_read_at' => 'datetime'
    ];

    /**
     * العلاقة مع المحادثة
     */
    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    /**
     * العلاقة مع المستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * تحديث وقت آخر قراءة
     */
    public function markAsRead()
    {
        $this->update(['last_read_at' => now()]);
    }

    /**
     * التحقق مما إذا كان المستخدم لديه رسائل غير مقروءة
     */
    public function hasUnreadMessages()
    {
        if (!$this->last_read_at) {
            return $this->chat->messages()->where('user_id', '!=', $this->user_id)->exists();
        }

        return $this->chat->messages()
            ->where('user_id', '!=', $this->user_id)
            ->where('created_at', '>', $this->last_read_at)
            ->exists();
    }

    /**
     * الحصول على عدد الرسائل غير المقروءة
     */
    public function unreadMessagesCount()
    {
        if (!$this->last_read_at) {
            return $this->chat->messages()
                ->where('user_id', '!=', $this->user_id)
                ->count();
        }

        return $this->chat->messages()
            ->where('user_id', '!=', $this->user_id)
            ->where('created_at', '>', $this->last_read_at)
            ->count();
    }

    /**
     * الحصول على عدد الرسائل غير المقروءة المرسلة من مستخدم معين
     */
    public function unreadMessagesFromUser($userId)
    {
        if (!$this->last_read_at) {
            return $this->chat->messages()
                ->where('user_id', $userId)
                ->count();
        }

        return $this->chat->messages()
            ->where('user_id', $userId)
            ->where('created_at', '>', $this->last_read_at)
            ->count();
    }

    /**
     * الحصول على الرسائل غير المقروءة
     */
    public function getUnreadMessages()
    {
        if (!$this->last_read_at) {
            return $this->chat->messages()
                ->where('user_id', '!=', $this->user_id)
                ->get();
        }

        return $this->chat->messages()
            ->where('user_id', '!=', $this->user_id)
            ->where('created_at', '>', $this->last_read_at)
            ->get();
    }

    /**
     * التحقق من حالة قراءة رسالة محددة
     */
    public function hasReadMessage($messageId)
    {
        $message = $this->chat->messages()->find($messageId);

        if (!$message) {
            return false;
        }

        return $this->last_read_at && $this->last_read_at >= $message->created_at;
    }
}