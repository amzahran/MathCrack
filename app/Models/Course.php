<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'level_id',
        'image',
        'price',
        'tests_price',
        'access_duration_days'
    ];

    protected $casts = [
        'tests_price' => 'decimal:2',
        'access_duration_days' => 'integer'
    ];


    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function lectures()
    {
        return $this->hasMany(Lecture::class);
    }

    /**
     * Get all tests for this course
     */
    public function tests()
    {
        return $this->hasMany(Test::class);
    }

    /**
     * Get active tests for this course
     */
    public function activeTests()
    {
        return $this->hasMany(Test::class)->where('is_active', true);
    }

    /**
     * التحقق من وجود حد زمني للوصول
     */
    public function hasAccessDurationLimit()
    {
        return $this->access_duration_days > 0;
    }

    /**
     * الحصول على مدة الوصول المسموحة بالأيام
     */
    public function getAccessDurationDays()
    {
        return $this->access_duration_days ?? 90; // القيمة الافتراضية 90 يوم
    }

    /**
     * الحصول على نص مدة الوصول المنسق
     */
    public function getFormattedAccessDuration()
    {
        if (!$this->hasAccessDurationLimit()) {
            return __('l.Unlimited');
        }

        $days = $this->getAccessDurationDays();

        if ($days >= 365) {
            $years = floor($days / 365);
            return $years . ' ' . ($years == 1 ? __('l.Year') : __('l.Years'));
        } elseif ($days >= 30) {
            $months = floor($days / 30);
            return $months . ' ' . ($months == 1 ? __('l.Month') : __('l.Months'));
        } else {
            return $days . ' ' . ($days == 1 ? __('l.Day') : __('l.Days'));
        }
    }
public function scopeActive($query)
{
    return $query->where('is_active', 1);   // أو status حسب عمودك
}



}
