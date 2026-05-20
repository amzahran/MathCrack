<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LectureQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_text',
        'type',
        'lecture_assignment_id',
        'points',
        'difficulty',
        'correct_answer',
        'explanation',
        'explanation_image',
        'question_image',
        'order',
    ];

    public function lectureAssignment()
    {
        return $this->belongsTo(LectureAssignment::class);
    }

    public function options()
    {
        return $this->hasMany(LectureQuestionOption::class)->orderBy('order');
    }

    public function studentAnswers()
    {
        return $this->hasMany(StudentLectureAnswer::class);
    }

    public function getCorrectOptionsAttribute()
    {
        return $this->options()->where('is_correct', true)->get();
    }

    public function isCorrectAnswer($answer)
    {
        switch ($this->type) {
            case 'mcq':
                $correctOption = $this->options()->where('is_correct', true)->first();
                return $correctOption && $correctOption->id == $answer;

            case 'tf':
                return strtolower($answer) === strtolower($this->correct_answer);

            case 'numeric':
                $correctAlternatives = $this->parseNumericCorrectAnswerAlternatives($this->correct_answer);
                $studentTokens = $this->parseNumericAnswerTokens($answer);

                foreach ($correctAlternatives as $correctTokens) {
                    if ($this->numericAnswerTokensMatch($correctTokens, $studentTokens)) {
                        return true;
                    }
                }

                return false;

            case 'essay':
                // المقالي يحتاج تقييم يدوي
                return null;

            default:
                return false;
        }
    }

    private function parseNumericCorrectAnswerAlternatives($answer): array
    {
        $answer = trim((string) $answer);

        if ($answer === '') {
            return [];
        }

        $alternatives = preg_split('/\s+\bor\b\s+|[,;|]+/i', $answer);
        $parsedAlternatives = [];

        foreach ($alternatives as $alternative) {
            $tokens = $this->parseNumericAnswerTokens($alternative);

            if ($tokens !== null) {
                $parsedAlternatives[] = $tokens;
            }
        }

        return $parsedAlternatives;
    }

    private function parseNumericAnswerTokens($answer): ?array
    {
        $answer = trim((string) $answer);

        if ($answer === '') {
            return null;
        }

        $tokens = preg_split('/\s+/', $answer);
        $values = [];

        foreach ($tokens as $token) {
            $value = $this->parseNumericAnswerToken($token);

            if ($value === null) {
                return null;
            }

            $values[] = $value;
        }

        return $values;
    }

    private function parseNumericAnswerToken(string $token): ?float
    {
        $token = trim($token);

        if ($token === '') {
            return null;
        }

        if (preg_match('/^-?(?:\d+(?:\.\d*)?|\.\d+)$/', $token)) {
            return (float) $token;
        }

        if (preg_match('/^(-?(?:\d+(?:\.\d*)?|\.\d+))\/(-?(?:\d+(?:\.\d*)?|\.\d+))$/', $token, $matches)) {
            $denominator = (float) $matches[2];

            if (abs($denominator) < 0.000000000001) {
                return null;
            }

            return (float) $matches[1] / $denominator;
        }

        return null;
    }

    private function numericAnswerTokensMatch(?array $correctTokens, ?array $studentTokens): bool
    {
        if ($correctTokens === null || $studentTokens === null) {
            return false;
        }

        if (count($correctTokens) !== count($studentTokens)) {
            return false;
        }

        $tolerance = 0.000001;

        foreach ($correctTokens as $index => $correctValue) {
            if (abs($correctValue - $studentTokens[$index]) > $tolerance) {
                return false;
            }
        }

        return true;
    }
}
