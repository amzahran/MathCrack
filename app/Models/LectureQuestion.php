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
        'correct_answer',
        'explanation',
        'question_image',
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
                return (float)$answer === (float)$this->correct_answer;

            case 'essay':
                // المقالي يحتاج تقييم يدوي
                return null;

            default:
                return false;
        }
    }
}