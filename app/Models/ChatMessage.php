<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = [
        'chat_id',
        'user_id',
        'content',
        'attachment',
        'attachment_type',
    ];

    /**
     * العلاقة مع المحادثة
     */
    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    /**
     * العلاقة مع المستخدم الذي أرسل الرسالة
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * الحصول على مسار المرفق الكامل
     */
    public function getAttachmentPathAttribute()
    {
        if (!$this->attachment) {
            return null;
        }
        return asset($this->attachment);
    }

    /**
     * التحقق مما إذا كانت الرسالة تحتوي على مرفق
     */
    public function hasAttachment()
    {
        return !empty($this->attachment);
    }

    /**
     * الحصول على نوع المرفق بطريقة سهلة
     */
    public function getAttachmentTypeDisplayAttribute()
    {
        $types = [
            'image' => 'صورة',
            'document' => 'مستند',
            'audio' => 'تسجيل صوتي',
            'video' => 'فيديو'
        ];

        return $types[$this->attachment_type] ?? 'مرفق';
    }
}