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
        'started_at'                => 'datetime',
        'current_module_started_at' => 'datetime',
        'paused_at'                 => 'datetime',
        'part1_started_at'          => 'datetime',
        'part1_ended_at'            => 'datetime',
        'break_started_at'          => 'datetime',
        'part2_started_at'          => 'datetime',
        'completed_at'              => 'datetime',
        'submitted_at'              => 'datetime',
        'progress_data'             => 'array',
        'is_paused'                 => 'boolean',
    ];

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

    public function part1Answers(): HasMany
    {
        return $this->answers()
            ->whereHas('question', function ($query) {
                $query->where('part', 'part1');
            });
    }

    public function part2Answers(): HasMany
    {
        return $this->answers()
            ->whereHas('question', function ($query) {
                $query->where('part', 'part2');
            });
    }

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

    public function isTimeUp(): bool
    {
        return $this->getRemainingTimeSeconds() <= 0;
    }

    public function calculateRawPoints(): float
    {
        return (float) $this->answers()->sum('score_earned');
    }

    public function calculateCurrentScore(): float
    {
        $rawPoints = $this->calculateRawPoints();

        if ($rawPoints <= 0) {
            return 0;
        }

        $roundedPoints = ceil($rawPoints / 10) * 10;

        return (float) min(600, $roundedPoints);
    }

    public function calculateFinalScore(): float
    {
        $pointsEarned = $this->calculateCurrentScore();

        return (float) min(800, 200 + $pointsEarned);
    }

    public function updateCurrentScore(): void
    {
        $pointsEarned = $this->calculateCurrentScore();
        $finalScore = min(800, 200 + $pointsEarned);

        $this->update([
            'current_score' => $pointsEarned,
            'final_score'   => $finalScore,
        ]);
    }

    public function updateScore(): void
    {
        $this->updateCurrentScore();
    }
}