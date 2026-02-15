<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestModule extends Model
{
    protected $fillable = [
        'test_id',
        'name',
        'description',
        'order',
        'questions_count',
        'time_minutes',
        'break_after_minutes'
    ];
    
    protected $casts = [
        'order' => 'integer',
        'questions_count' => 'integer',
        'time_minutes' => 'integer',
        'break_after_minutes' => 'integer',
    ];
    
    protected $appends = [
        'max_points',
        'has_break',
        'is_last_module',
        'next_module_id',
        'previous_module_id'
    ];
    
    /**
     * العلاقة مع الاختبار
     */
    public function test()
    {
        return $this->belongsTo(Test::class);
    }
    
    /**
     * العلاقة مع الأسئلة
     */
    public function questions()
    {
        return $this->hasMany(TestQuestion::class, 'module_number', 'order')
            ->where('test_id', $this->test_id);
    }
    
    /**
     * الحصول على النقاط العظمى
     */
    public function getMaxPointsAttribute()
    {
        if (!$this->test) {
            return 0;
        }
        return $this->test->default_question_score * $this->questions_count;
    }
    
    /**
     * التحقق إذا كان هناك استراحة بعد الموديول
     */
    public function getHasBreakAttribute()
    {
        return $this->break_after_minutes > 0;
    }
    
    /**
     * الحصول على الموديول التالي
     */
    public function nextModule()
    {
        return $this->test->modules()
            ->where('order', '>', $this->order)
            ->orderBy('order')
            ->first();
    }
    
    /**
     * الحصول على الموديول السابق
     */
    public function previousModule()
    {
        return $this->test->modules()
            ->where('order', '<', $this->order)
            ->orderBy('order', 'desc')
            ->first();
    }
    
    /**
     * التحقق إذا كان هذا آخر موديول
     */
    public function getIsLastModuleAttribute()
    {
        if (!$this->test) {
            return true;
        }
        
        $maxOrder = $this->test->modules()->max('order');
        return $this->order == $maxOrder;
    }
    
    /**
     * الحصول على معرف الموديول التالي (للاستخدام في JSON)
     */
    public function getNextModuleIdAttribute()
    {
        $next = $this->nextModule();
        return $next ? $next->id : null;
    }
    
    /**
     * الحصول على معرف الموديول السابق (للاستخدام في JSON)
     */
    public function getPreviousModuleIdAttribute()
    {
        $prev = $this->previousModule();
        return $prev ? $prev->id : null;
    }
    
    /**
     * الحصول على جميع الموديولات التالية
     */
    public function upcomingModules()
    {
        return $this->test->modules()
            ->where('order', '>', $this->order)
            ->orderBy('order')
            ->get();
    }
    
    /**
     * الحصول على تقدم الموديول كنسبة مئوية
     */
    public function getProgressPercentage($answeredQuestions)
    {
        if ($this->questions_count == 0) {
            return 100;
        }
        
        return min(100, ($answeredQuestions / $this->questions_count) * 100);
    }
    
    /**
     * التحقق إذا كان الموديول مكتملاً (جميع الأسئلة مجابة)
     */
    public function isCompleted($answeredQuestions)
    {
        return $answeredQuestions >= $this->questions_count;
    }
    
    /**
     * نطاق للحصول على الموديول الأول
     */
    public function scopeFirstModule($query)
    {
        return $query->orderBy('order')->first();
    }
    
    /**
     * نطاق للحصول على الموديول الأخير
     */
    public function scopeLastModule($query)
    {
        return $query->orderBy('order', 'desc')->first();
    }
}