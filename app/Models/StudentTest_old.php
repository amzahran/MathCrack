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
        'part1_started_at',
        'part1_ended_at',
        'break_started_at',
        'part2_started_at',
        'completed_at',
        'time_spent_part1',
        'time_spent_part2'
    ];

    protected $casts = [
        'student_id' => 'integer',
        'test_id' => 'integer',
        'attempt_number' => 'integer',
        'current_score' => 'decimal:2',
        'final_score' => 'decimal:2',
        'started_at' => 'datetime',
        'part1_started_at' => 'datetime',
        'part1_ended_at' => 'datetime',
        'break_started_at' => 'datetime',
        'part2_started_at' => 'datetime',
        'completed_at' => 'datetime',
        'time_spent_part1' => 'integer',
        'time_spent_part2' => 'integer'
    ];

    /**
     * Get the attempt number with default value
     */
    public function getAttemptNumberAttribute($value)
    {
        return $value ?? 1;
    }

    /**
     * Get the student that owns this test attempt
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the test being attempted
     */
    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    /**
     * Get all answers for this test attempt
     */
    public function answers(): HasMany
    {
        return $this->hasMany(StudentTestAnswer::class);
    }

    /**
     * Get answers for part 1
     */
    public function part1Answers(): HasMany
    {
        return $this->hasMany(StudentTestAnswer::class)
            ->whereHas('question', function ($query) {
                $query->where('part', 'part1');
            });
    }

    /**
     * Get answers for part 2
     */
    public function part2Answers(): HasMany
    {
        return $this->hasMany(StudentTestAnswer::class)
            ->whereHas('question', function ($query) {
                $query->where('part', 'part2');
            });
    }

    /**
     * Check if test is in progress
     */
    public function isInProgress(): bool
    {
        return in_array($this->status, ['part1_in_progress', 'break_time', 'part2_in_progress']);
    }

    /**
     * Check if test is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if student is in break time
     */
    public function isInBreak(): bool
    {
        return $this->status === 'break_time';
    }

    /**
     * Check if student is in part 1
     */
    public function isInPart1(): bool
    {
        return $this->status === 'part1_in_progress';
    }

    /**
     * Check if student is in part 2
     */
    public function isInPart2(): bool
    {
        return $this->status === 'part2_in_progress';
    }

    /**
     * Start the test
     */
    public function startTest(): void
    {
        $this->update([
            'status' => 'part1_in_progress',
            'started_at' => now(),
            'part1_started_at' => now()
        ]);
    }

    /**
     * End part 1 and start break
     */
    public function endPart1(): void
    {
        $timeSpent = now()->diffInSeconds($this->part1_started_at);

        $this->update([
            'status' => 'break_time',
            'part1_ended_at' => now(),
            'break_started_at' => now(),
            'time_spent_part1' => $timeSpent
        ]);
    }

    /**
     * Skip break and start part 2
     */
    public function startPart2(): void
    {
        $this->update([
            'status' => 'part2_in_progress',
            'part2_started_at' => now()
        ]);
    }

    /**
     * Complete the test
     */
    public function completeTest(): void
    {
        $timeSpent = null;
        if ($this->part2_started_at) {
            $timeSpent = now()->diffInSeconds($this->part2_started_at);
        }

        // Calculate final score
        $totalEarned = $this->answers()->sum('score_earned');
        $finalScore = $this->test->initial_score + $totalEarned;

        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'time_spent_part2' => $timeSpent,
            'final_score' => $finalScore
        ]);
    }

    /**
     * Get remaining time for current part in seconds
     */
    public function getRemainingTimeSeconds(): int
    {
        if ($this->isInPart1() && $this->part1_started_at) {
            $elapsed = now()->diffInSeconds($this->part1_started_at);
            $total = $this->test->part1_time_minutes * 60;
            return max(0, $total - $elapsed);
        }

        if ($this->isInPart2() && $this->part2_started_at) {
            $elapsed = now()->diffInSeconds($this->part2_started_at);
            $total = $this->test->part2_time_minutes * 60;
            return max(0, $total - $elapsed);
        }

        if ($this->isInBreak() && $this->break_started_at) {
            $elapsed = now()->diffInSeconds($this->break_started_at);
            $total = $this->test->break_time_minutes * 60;
            return max(0, $total - $elapsed);
        }

        return 0;
    }

    /**
     * Check if time is up for current part
     */
    public function isTimeUp(): bool
    {
        return $this->getRemainingTimeSeconds() <= 0;
    }

    /**
     * Get progress percentage
     */
    public function getProgressPercentage(): float
    {
        $totalQuestions = $this->test->total_questions_count;
        $answeredQuestions = $this->answers()->count();

        return $totalQuestions > 0 ? ($answeredQuestions / $totalQuestions) * 100 : 0;
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        return match($this->status) {
            'not_started' => 'Not Started',
            'part1_in_progress' => 'Part 1 in Progress',
            'break_time' => 'Break Time',
            'part2_in_progress' => 'Part 2 in Progress',
            'completed' => 'Completed',
            default => $this->status
        };
    }

    /**
     * Calculate current score based on answers
     */
    public function calculateCurrentScore(): float
    {
        $earnedScore = $this->answers()->sum('score_earned');
        return $this->test->initial_score + $earnedScore;
    }

    /**
     * Update current score
     */
    public function updateCurrentScore(): void
    {
        $this->update([
            'current_score' => $this->calculateCurrentScore()
        ]);
    }

    /**
     * Get remaining attempts for this test
     */
    public function getRemainingAttempts(): int
    {
        $maxAttempts = $this->test->max_attempts;
        $usedAttempts = StudentTest::where('student_id', $this->student_id)
            ->where('test_id', $this->test_id)
            ->where('status', 'completed')
            ->count();

        return max(0, $maxAttempts - $usedAttempts);
    }

    /**
     * Check if student can attempt this test again
     */
    public function canAttemptAgain(): bool
    {
        return $this->getRemainingAttempts() > 0;
    }

    /**
     * Get all attempts for this student and test
     */
    public function getAllAttempts()
    {
        return StudentTest::where('student_id', $this->student_id)
            ->where('test_id', $this->test_id)
            ->orderBy('attempt_number', 'desc')
            ->get();
    }

    /**
     * Get the latest completed attempt
     */
    public function getLatestCompletedAttempt()
    {
        return StudentTest::where('student_id', $this->student_id)
            ->where('test_id', $this->test_id)
            ->where('status', 'completed')
            ->orderBy('attempt_number', 'desc')
            ->first();
    }
}