<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = [
        'name',
        'is_group',
        'description',
        'created_by'
    ];

    /**
     * العلاقة مع المستخدم الذي أنشأ المحادثة
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * العلاقة مع المشاركين في المحادثة
     */
    public function participants()
    {
        return $this->hasMany(ChatParticipant::class);
    }

    /**
     * العلاقة مع رسائل المحادثة
     */
    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    /**
     * إضافة مستخدمين إلى المحادثة
     */
    public function addParticipants($userIds, $isAdmin = false)
    {
        $participants = [];
        foreach ($userIds as $userId) {
            $participants[] = [
                'chat_id' => $this->id,
                'user_id' => $userId,
                'is_admin' => $isAdmin,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        return ChatParticipant::insert($participants);
    }

    /**
     * الحصول على المستخدمين المشاركين في المحادثة
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'chat_participants')
            ->withPivot(['is_admin', 'last_read_at'])
            ->withTimestamps();
    }

    /**
     * التحقق مما إذا كان المستخدم مشارك في هذه المحادثة
     */
    public function hasParticipant($userId)
    {
        return $this->participants()->where('user_id', $userId)->exists();
    }
}