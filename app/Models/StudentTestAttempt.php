<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentTestAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'test_id',
        'score',
        'max_score',
        'percentage',
        'status',
        'started_at',
        'completed_at',
        'time_taken',
        'is_passed',
        'attempt_number',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'score' => 'integer',
        'max_score' => 'integer',
        'percentage' => 'float',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'time_taken' => 'integer',
        'is_passed' => 'boolean',
        'attempt_number' => 'integer'
    ];

    protected $appends = [
        'formatted_time_taken',
        'formatted_percentage',
        'is_completed'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(StudentTestAnswer::class, 'attempt_id');
    }

    public function getFormattedTimeTakenAttribute(): string
    {
        if (!$this->time_taken) {
            return '0:00';
        }
        
        $minutes = floor($this->time_taken / 60);
        $seconds = $this->time_taken % 60;
        
        return sprintf('%d:%02d', $minutes, $seconds);
    }

    public function getFormattedPercentageAttribute(): string
    {
        return number_format($this->percentage ?? 0, 1) . '%';
    }

    public function getIsCompletedAttribute(): bool
    {
        return in_array($this->status, ['completed', 'passed', 'failed']);
    }

    public function calculateScore(): array
    {
        if (!$this->relationLoaded('answers')) {
            $this->load('answers');
        }
        
        $totalScore = 0;
        $maxScore = 0;
        $correctAnswers = 0;
        $totalQuestions = 0;
        
        // إذا كان هناك إجابات
        if ($this->answers->count() > 0) {
            foreach ($this->answers as $answer) {
                $totalScore += $answer->score_earned;
                $maxScore += $answer->question->score ?? 0;
                $totalQuestions++;
                
                if ($answer->is_correct) {
                    $correctAnswers++;
                }
            }
            
            $percentage = $maxScore > 0 ? ($totalScore / $maxScore) * 100 : 0;
            $isPassed = $this->test->passing_score 
                ? $percentage >= $this->test->passing_score 
                : $percentage >= 60; // افتراضي 60%
        } else {
            // إذا لم تكن هناك إجابات
            if ($this->test->questions) {
                $maxScore = $this->test->questions->sum('score');
            }
            $percentage = 0;
            $isPassed = false;
        }
        
        return [
            'score' => $totalScore,
            'max_score' => $maxScore,
            'percentage' => $percentage,
            'correct_answers' => $correctAnswers,
            'total_questions' => $totalQuestions,
            'is_passed' => $isPassed
        ];
    }

    public function updateScore(): bool
    {
        $scores = $this->calculateScore();
        
        $this->update([
            'score' => $scores['score'],
            'max_score' => $scores['max_score'],
            'percentage' => $scores['percentage'],
            'is_passed' => $scores['is_passed'],
            'status' => $scores['is_passed'] ? 'passed' : 'failed'
        ]);
        
        return true;
    }

    public function complete(): bool
    {
        $this->updateScore();
        
        $this->update([
            'completed_at' => now(),
            'status' => $this->is_passed ? 'passed' : 'failed',
            'time_taken' => $this->started_at ? now()->diffInSeconds($this->started_at) : 0
        ]);
        
        return true;
    }

    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['completed', 'passed', 'failed']);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByTest($query, $testId)
    {
        return $query->where('test_id', $testId);
    }

    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('completed_at', 'desc')->limit($limit);
    }

    public function scopePassed($query)
    {
        return $query->where('is_passed', true);
    }

    public function getNextAttemptNumber($userId, $testId): int
    {
        $lastAttempt = self::where('user_id', $userId)
            ->where('test_id', $testId)
            ->orderBy('attempt_number', 'desc')
            ->first();
            
        return $lastAttempt ? $lastAttempt->attempt_number + 1 : 1;
    }
}