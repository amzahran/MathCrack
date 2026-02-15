<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'modules_count',
        'modules_data',
        'break_time_minutes',
        'max_attempts',
        'is_active',
        'part1_questions_count',
        'part1_time_minutes',
        'part2_questions_count',
        'part2_time_minutes',
    ];

    protected $casts = [
        'price'                  => 'decimal:2',
        'total_score'            => 'integer',
        'initial_score'          => 'integer',
        'default_question_score' => 'integer',
        'modules_count'          => 'integer',
        'modules_data'           => 'array',
        'part1_questions_count'  => 'integer',
        'part1_time_minutes'     => 'integer',
        'part2_questions_count'  => 'integer',
        'part2_time_minutes'     => 'integer',
        'break_time_minutes'     => 'integer',
        'max_attempts'           => 'integer',
        'is_active'              => 'boolean',
    ];

    // 1) هنا مباشرة بعد الـ casts ضع الدالة المساعدة
    protected function autoFillLegacyPartsFromModules(): void
    {
        $modules = $this->modules_data ?? [];

        if (is_string($modules)) {
            $decoded = json_decode($modules, true);
            $modules = is_array($decoded) ? $decoded : [];
        }

        if (!is_array($modules) || empty($modules)) {
            return;
        }

        $first  = $modules[0] ?? null;
        $second = $modules[1] ?? null;

        if ($first) {
            $this->part1_questions_count = (int)($first['questionscount'] ?? 0);
            $this->part1_time_minutes    = (int)($first['timeminutes'] ?? 0);
        }

        if ($second) {
            $this->part2_questions_count = (int)($second['questionscount'] ?? 0);
            $this->part2_time_minutes    = (int)($second['timeminutes'] ?? 0);
        } else {
            $this->part2_questions_count = 0;
            $this->part2_time_minutes    = 0;
        }
    }

    /**
     * Get the max attempts with default value
     */
    public function getMaxAttemptsAttribute($value)
    {
        return $value ?? 1;
    }

    // ... باقي العلاقات والدوال الموجودة عندك ...

    // 2) في آخر الكلاس (قبل آخر } مباشرة) ضع booted
    
    /**
     * Get the max attempts with default value
     */
    

    /**
     * Get the course that owns the test
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get all questions for this test
     */
    public function questions(): HasMany
    {
        return $this->hasMany(TestQuestion::class)->orderBy('part')->orderBy('question_order');
    }

    /**
     * Get questions for part 1
     */
    public function part1Questions(): HasMany
    {
        return $this->hasMany(TestQuestion::class)->where('part', 'part1')->orderBy('question_order');
    }

    /**
     * Get questions for part 2
     */
    public function part2Questions(): HasMany
    {
        return $this->hasMany(TestQuestion::class)->where('part', 'part2')->orderBy('question_order');
    }

    /**
     * Get all student tests for this test
     */
    public function studentTests(): HasMany
    {
        return $this->hasMany(StudentTest::class);
    }

    /**
     * Get completed student tests for this test
     */
    public function completedStudentTests(): HasMany
    {
        return $this->hasMany(StudentTest::class)->where('status', 'completed');
    }

    /**
     * Check if test name is unique within the course
     */
    public static function isNameUniqueInCourse(string $name, int $courseId, ?int $excludeTestId = null): bool
    {
        $query = static::where('name', $name)->where('course_id', $courseId);

        if ($excludeTestId) {
            $query->where('id', '!=', $excludeTestId);
        }

        return !$query->exists();
    }

    /**
     * Get the total questions count
     */
    public function getTotalQuestionsCountAttribute(): int
    {
        return $this->part1_questions_count + $this->part2_questions_count;
    }

    /**
     * Get the total test time in minutes
     */
    public function getTotalTimeMinutesAttribute(): int
    {
        return $this->part1_time_minutes + $this->part2_time_minutes + $this->break_time_minutes;
    }

    /**
     * Check if all questions have been added
     */
    public function areAllQuestionsAdded(): bool
    {
        $part1Count = $this->questions()->where('part', 'part1')->count();
        $part2Count = $this->questions()->where('part', 'part2')->count();

        return $part1Count === $this->part1_questions_count &&
               $part2Count === $this->part2_questions_count;
    }

    /**
     * Get next question order for a specific part
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
     * Check if more questions can be added to a part
     */
    public function canAddQuestionToPart(string $part): bool
    {
        $currentCount = $this->questions()->where('part', $part)->count();
        $maxCount = $part === 'part1' ? $this->part1_questions_count : $this->part2_questions_count;

        return $currentCount < $maxCount;
    }

    /**
     * Get questions count for a specific part
     */
    public function getQuestionsCountForPart(string $part): int
    {
        return $this->questions()->where('part', $part)->count();
    }

    protected static function booted()
    {
        static::saving(function (Test $test) {
            if (!empty($test->modules_data)) {
                $test->autoFillLegacyPartsFromModules();
            }
        });
    }
}

