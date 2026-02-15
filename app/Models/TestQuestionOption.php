<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestQuestionOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_question_id',
        'option_text',
        'option_image',
        'is_correct',
        'option_order'
    ];

    protected $casts = [
        'test_question_id' => 'integer',
        'is_correct' => 'boolean',
        'option_order' => 'integer'
    ];

    /**
     * Get the question that owns this option
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(TestQuestion::class, 'test_question_id');
    }

    /**
     * Get all student answers that selected this option
     */
    public function studentAnswers(): HasMany
    {
        return $this->hasMany(StudentTestAnswer::class, 'selected_option_id');
    }

    /**
     * Get the option letter (A, B, C, D, etc.)
     */
    public function getOptionLetterAttribute(): string
    {
        $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        $index = $this->option_order - 1;

        return $letters[$index] ?? chr(65 + $index);
    }

    /**
     * Scope to get only correct options
     */
    public function scopeCorrect($query)
    {
        return $query->where('is_correct', true);
    }

    /**
     * Scope to get only incorrect options
     */
    public function scopeIncorrect($query)
    {
        return $query->where('is_correct', false);
    }
}
