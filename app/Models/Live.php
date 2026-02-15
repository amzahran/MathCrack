<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Live extends Model
{
    protected $table = 'lives';

    protected $guarded = [];

    protected $casts = [
        'start_at' => 'datetime',
        'duration' => 'integer',
        'price' => 'decimal:2'
    ];

    /**
     * العلاقة مع الكورس
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * العلاقة مع الفواتير
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'type_value')
            ->where('category', 'live')
            ->where('type', 'single');
    }

    /**
     * العلاقة مع الطلاب الذين اشتروا هذا البث المباشر
     */
    public function students()
    {
        return $this->belongsToMany(User::class, 'invoices', 'type_value', 'user_id')
            ->where('invoices.category', 'live')
            ->where('invoices.type', 'single')
            ->where('invoices.status', 'paid')
            ->withPivot(['amount', 'created_at']);
    }

    /**
     * التحقق من أن البث المباشر مجاني
     */
    public function isFree()
    {
        return $this->type === 'free' || $this->price === null || $this->price == 0;
    }

    /**
     * التحقق من أن البث المباشر مدفوع
     */
    public function isPaid()
    {
        return $this->type === 'price' && $this->price > 0;
    }

    /**
     * التحقق من أن البث المباشر شهري
     */
    public function isMonthly()
    {
        return $this->type === 'month';
    }

    /**
     * التحقق من أن البث المباشر تخص الكورس
     */
    public function isCourseBased()
    {
        return $this->type === 'course';
    }

    /**
     * التحقق من أن البث المباشر بدأ
     */
    public function hasStarted()
    {
        return $this->start_at && Carbon::now()->gte($this->start_at);
    }

    /**
     * التحقق من أن البث المباشر انتهى
     */
    public function hasEnded()
    {
        if (!$this->start_at || !$this->duration) {
            return false;
        }
        return Carbon::now()->gte($this->start_at->addMinutes($this->duration));
    }

    /**
     * التحقق من أن البث المباشر نشط الآن
     */
    public function isActive()
    {
        return $this->hasStarted() && !$this->hasEnded();
    }

    /**
     * التحقق من أن البث المباشر قادم
     */
    public function isUpcoming()
    {
        return $this->start_at && Carbon::now()->lt($this->start_at);
    }

    /**
     * الحصول على السعر المنسق
     */
    public function getFormattedPriceAttribute()
    {
        if ($this->isFree()) {
            return __('l.Free');
        }
        return number_format($this->price, 2) . ' ' . __('l.Currency');
    }

    /**
     * الحصول على مدة البث المباشر المنسقة
     */
    public function getFormattedDurationAttribute()
    {
        if (!$this->duration) {
            return __('l.Not specified');
        }

        if ($this->duration < 60) {
            return $this->duration . ' ' . __('l.minutes');
        }

        $hours = floor($this->duration / 60);
        $minutes = $this->duration % 60;

        if ($minutes == 0) {
            return $hours . ' ' . __('l.hours');
        }

        return $hours . ' ' . __('l.hours') . ' ' . $minutes . ' ' . __('l.minutes');
    }

    /**
     * الحصول على نوع البث المباشر المترجم
     */
    public function getTypeDisplayAttribute()
    {
        return match($this->type) {
            'free' => __('l.Free'),
            'price' => __('l.Paid'),
            'month' => __('l.Monthly'),
            'course' => __('l.Course'),
            default => ucfirst($this->type)
        };
    }

    /**
     * الحصول على حالة البث المباشر
     */
    public function getStatusAttribute()
    {
        if ($this->isUpcoming()) {
            return __('l.Upcoming');
        }

        if ($this->isActive()) {
            return __('l.Live Now');
        }

        if ($this->hasEnded()) {
            return __('l.Ended');
        }

        return __('l.Scheduled');
    }

    /**
     * الحصول على الوقت المتبقي حتى البداية
     */
    public function getTimeUntilStartAttribute()
    {
        if (!$this->start_at || $this->hasStarted()) {
            return null;
        }

        $now = Carbon::now();
        $startTime = Carbon::parse($this->start_at);
        $diff = $now->diff($startTime);

        if ($diff->days > 0) {
            return $diff->days . ' ' . __('l.days') . ' ' . $diff->h . ' ' . __('l.hours');
        } elseif ($diff->h > 0) {
            return $diff->h . ' ' . __('l.hours') . ' ' . $diff->i . ' ' . __('l.minutes');
        } else {
            return $diff->i . ' ' . __('l.minutes');
        }
    }

    /**
     * الحصول على عدد المشاركين الحاليين
     */
    public function getCurrentParticipantsCountAttribute()
    {
        return $this->students()->count();
    }

    /**
     * التحقق من إمكانية الانضمام
     */
    public function canJoin($userId = null)
    {
        if ($this->hasEnded()) {
            return false;
        }

        return true;
    }
}
