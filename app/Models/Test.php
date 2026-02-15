<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'course_id',
        'price',
        'total_score',
        'initial_score',
        'default_question_score',
        'part1_questions_count',
        'part1_time_minutes',
        'part2_questions_count',
        'part2_time_minutes',
        'part3_questions_count',
        'part3_time_minutes',
        'part4_questions_count',
        'part4_time_minutes',
        'part5_questions_count',
        'part5_time_minutes',
        'break_time_minutes',
        'max_attempts',
        'is_active',
    ];

    protected $casts = [
        'price'                  => 'decimal:2',
        'total_score'            => 'integer',
        'initial_score'          => 'integer',
        'default_question_score' => 'integer',
        'part1_questions_count'  => 'integer',
        'part1_time_minutes'     => 'integer',
        'part2_questions_count'  => 'integer',
        'part2_time_minutes'     => 'integer',
        'break_time_minutes'     => 'integer',
        'max_attempts'           => 'integer',
        'is_active'              => 'boolean',
    ];

    /**
     * Max attempts مع قيمة افتراضية
     */
    public function getMaxAttemptsAttribute($value)
    {
        return $value ?? 1;
    }

    /**
     * الكورس التابع له الاختبار
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * الموديولات (Modules) المرتبطة بالاختبار
     */
    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class, 'test_module', 'test_id', 'module_id')
                    ->withTimestamps()
                    ->withPivot(['order'])
                    ->orderBy('test_module.order');
    }

    /**
     * كل الأسئلة في الاختبار
     */
    public function questions(): HasMany
    {
        return $this->hasMany(TestQuestion::class)
            ->orderBy('part')
            ->orderBy('question_order');
    }

    /**
     * أسئلة الجزء الأول
     */
    public function part1Questions(): HasMany
    {
        return $this->hasMany(TestQuestion::class)
            ->where('part', 'part1')
            ->orderBy('question_order');
    }

    /**
     * أسئلة الجزء الثاني
     */
    public function part2Questions(): HasMany
    {
        return $this->hasMany(TestQuestion::class)
            ->where('part', 'part2')
            ->orderBy('question_order');
    }

    /**
     * كل سجلات StudentTest لهذا الاختبار
     */
    public function studentTests(): HasMany
    {
        return $this->hasMany(StudentTest::class);
    }

    /**
     * كل المحاولات Attempts لهذا الاختبار
     * (نستخدم نفس موديل StudentTest لأنه يمثّل المحاولة)
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(StudentTest::class);
    }

    /**
     * فقط المحاولات المكتملة
     */
    public function completedStudentTests(): HasMany
    {
        return $this->hasMany(StudentTest::class)
            ->where('status', 'completed');
    }

    /**
     * التأكد من أن اسم الاختبار فريد داخل نفس الكورس
     */
    public static function isNameUniqueInCourse(string $name, int $courseId, ?int $excludeTestId = null): bool
    {
        $query = static::where('name', $name)
            ->where('course_id', $courseId);

        if ($excludeTestId) {
            $query->where('id', '!=', $excludeTestId);
        }

        return !$query->exists();
    }

    /**
     * إجمالي عدد الأسئلة
     */
    public function getTotalQuestionsCountAttribute(): int
    {
        return $this->part1_questions_count + $this->part2_questions_count;
    }

    /**
     * إجمالي زمن الاختبار بالدقائق
     */
    public function getTotalTimeMinutesAttribute(): int
    {
        return $this->part1_time_minutes
             + $this->part2_time_minutes
             + $this->break_time_minutes;
    }

    /**
     * هل تمت إضافة كل الأسئلة؟
     */
    public function areAllQuestionsAdded(): bool
    {
        $part1Count = $this->questions()->where('part', 'part1')->count();
        $part2Count = $this->questions()->where('part', 'part2')->count();

        return $part1Count === $this->part1_questions_count
            && $part2Count === $this->part2_questions_count;
    }

    /**
     * ترتيب السؤال التالي لجزء معيّن
     */
    public function getNextQuestionOrder(string $part): int
    {
        $lastQuestion = $this->questions()
            ->where('part', $part)
            ->orderBy('question_order', 'desc')
            ->first();

        return $lastQuestion ? $lastQuestion->question_order + 1 : 1;
    }

    /**
     * هل ما زال يمكن إضافة سؤال لجزء معيّن؟
     */
    public function canAddQuestionToPart(string $part): bool
    {
        $currentCount = $this->questions()->where('part', $part)->count();
        $maxCount = $part === 'part1'
            ? $this->part1_questions_count
            : $this->part2_questions_count;

        return $currentCount < $maxCount;
    }

    /**
     * عدد الأسئلة في جزء معيّن
     */
    public function getQuestionsCountForPart(string $part): int
    {
        return $this->questions()->where('part', $part)->count();
    }
    public function getParts()
{
    $parts = [];

    for ($i = 1; $i <= 5; $i++) {

        $questions = $this->{"part{$i}_questions_count"};
        $time      = $this->{"part{$i}_time_minutes"};

        // لو الموديول ملوش أسئلة تجاهله
        if ($questions > 0) {
            $parts[$i] = [
                'part'         => $i,
                'questions'    => $questions,
                'time_minutes' => $time,
            ];
        }
    }

    return $parts;
}

public function isInModule($moduleNumber)
{
    return $this->current_module == $moduleNumber;
}

public function getCurrentModuleName()
{
    $modules = [
        1 => 'الجزء الأول',
        2 => 'الجزء الثاني',
        3 => 'الجزء الثالث',
        4 => 'الجزء الرابع',
        // ... إلخ
    ];
    
    return $modules[$this->current_module] ?? "الجزء {$this->current_module}";
}
}