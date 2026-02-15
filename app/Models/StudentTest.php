<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'test_id',
        'attempt_number',
        'status',
        'current_score',
        'final_score',
        'started_at',
        'current_module',
        'current_module_started_at',
        'remaining_seconds',
        'is_paused',
        'paused_at',
        'paused_seconds',
        'progress_data',
        'part1_started_at',
        'part1_ended_at',
        'break_started_at',
        'part2_started_at',
        'completed_at',
        'submitted_at',
        'time_spent_part1',
        'time_spent_part2',
    ];

    protected $casts = [
        'started_at'               => 'datetime',
        'current_module_started_at'=> 'datetime',
        'paused_at'                => 'datetime',
        'part1_started_at'         => 'datetime',
        'part1_ended_at'           => 'datetime',
        'break_started_at'         => 'datetime',
        'part2_started_at'         => 'datetime',
        'completed_at'             => 'datetime',
        'submitted_at'             => 'datetime',
        'progress_data'            => 'array',
        'is_paused'                => 'boolean',
    ];

    // العلاقات
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(StudentTestAnswer::class);
    }

    // أسئلة الجزء الأول
    public function part1Answers(): HasMany
    {
        return $this->answers()
            ->whereHas('question', function ($query) {
                $query->where('part', 'part1');
            });
    }

    // أسئلة الجزء الثاني
    public function part2Answers(): HasMany
    {
        return $this->answers()
            ->whereHas('question', function ($query) {
                $query->where('part', 'part2');
            });
    }

    // حالات الاختبار
    public function isInPart1(): bool
    {
        return $this->status === 'part1_in_progress';
    }

    public function isInBreak(): bool
    {
        return $this->status === 'break_time';
    }

    public function isInPart2(): bool
    {
        return $this->status === 'part2_in_progress';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isInProgress(): bool
    {
        return in_array($this->status, [
            'part1_in_progress',
            'break_time',
            'part2_in_progress',
        ]);
    }

    // حساب الوقت المتبقي (ثواني)
    public function getRemainingTimeSeconds(): int
    {
        $test = $this->test;
        if (!$test) {
            return 0;
        }

        $now = now();

        if ($this->isInPart1()) {
            $start = $this->part1_started_at ?? $this->started_at;
            if (!$start) {
                return 0;
            }

            $total = (int) ($test->part1_time_minutes ?? 0) * 60;
            $elapsed = $start->diffInSeconds($now);

            return max(0, $total - $elapsed);
        }

        if ($this->isInBreak()) {
            $start = $this->break_started_at;
            if (!$start) {
                return 0;
            }

            $total = (int) ($test->break_time_minutes ?? 0) * 60;
            $elapsed = $start->diffInSeconds($now);

            return max(0, $total - $elapsed);
        }

        if ($this->isInPart2()) {
            $start = $this->part2_started_at;
            if (!$start) {
                return 0;
            }

            $total = (int) ($test->part2_time_minutes ?? 0) * 60;
            $elapsed = $start->diffInSeconds($now);

            return max(0, $total - $elapsed);
        }

        return 0;
    }

    // هل الوقت انتهى
    public function isTimeUp(): bool
    {
        return $this->getRemainingTimeSeconds() <= 0;
    }

    // حساب الدرجة الحالية
    public function calculateCurrentScore(): float
    {
        $earned = $this->answers()->sum('score_earned');

        return (float) $earned;
    }

    // تحديث الدرجة الحالية
    public function updateCurrentScore(): void
    {
        $this->update([
            'current_score' => $this->calculateCurrentScore(),
        ]);
    }
}
