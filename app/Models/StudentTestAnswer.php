<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentTestAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_test_id',
        'test_question_id',
        'selected_option_id',
        'answer_text',
        'is_correct',
        'score_earned',
        'answered_at'
    ];

    protected $casts = [
        'student_test_id' => 'integer',
        'test_question_id' => 'integer',
        'selected_option_id' => 'integer',
        'is_correct' => 'boolean',
        'score_earned' => 'decimal:2',
        'answered_at' => 'datetime'
    ];

    /**
     * Get the student test that owns this answer
     */
    public function studentTest(): BelongsTo
    {
        return $this->belongsTo(StudentTest::class);
    }

    /**
     * Get the question being answered
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(TestQuestion::class, 'test_question_id');
    }

    /**
     * Get the selected option (for MCQ questions)
     */
    public function selectedOption(): BelongsTo
    {
        return $this->belongsTo(TestQuestionOption::class, 'selected_option_id');
    }

    /**
     * Get the student who gave this answer
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Check if this answer is correct
     */
    public function isCorrect(): bool
    {
        return $this->is_correct === true;
    }

    /**
     * Get the answer display text
     */
    public function getAnswerDisplayAttribute(): string
    {
        switch ($this->question->type) {
            case 'mcq':
                return $this->selectedOption ? $this->selectedOption->option_text : 'Not selected';

            case 'tf':
                if ($this->answer_text === 'true') {
                    return 'True';
                } elseif ($this->answer_text === 'false') {
                    return 'False';
                } else {
                    return $this->answer_text ?? 'Not answered';
                }

            case 'numeric':
                return $this->answer_text ?? 'Not answered';

            default:
                return $this->answer_text ?? 'Not answered';
        }
    }

    /**
     * Save answer with validation
     */
    public static function saveAnswer(int $studentTestId, int $questionId, $answer): self
    {
        $question = TestQuestion::findOrFail($questionId);
        $validation = $question->validateAnswer($answer);

        $answerData = [
            'student_test_id' => $studentTestId,
            'test_question_id' => $questionId,
            'is_correct' => $validation['is_correct'],
            'score_earned' => $validation['score_earned'],
            'answered_at' => now()
        ];

        // Set answer based on question type
        switch ($question->type) {
            case 'mcq':
                $answerData['selected_option_id'] = $answer;
                break;
            case 'tf':
            case 'numeric':
                $answerData['answer_text'] = $answer;
                break;
        }

        return static::updateOrCreate(
            [
                'student_test_id' => $studentTestId,
                'test_question_id' => $questionId
            ],
            $answerData
        );
    }

    /**
     * Scope to get only correct answers
     */
    public function scopeCorrect($query)
    {
        return $query->where('is_correct', true);
    }

    /**
     * Scope to get only incorrect answers
     */
    public function scopeIncorrect($query)
    {
        return $query->where('is_correct', false);
    }
}