<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentLectureAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_lecture_assignment_id',
        'lecture_question_id',
        'answer_text',
        'selected_option_id',
        'is_correct',
        'points_earned',
        'teacher_feedback',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function studentLectureAssignment()
    {
        return $this->belongsTo(StudentLectureAssignment::class);
    }

    public function lectureQuestion()
    {
        return $this->belongsTo(LectureQuestion::class);
    }

    public function selectedOption()
    {
        return $this->belongsTo(LectureQuestionOption::class, 'selected_option_id');
    }

    public function getStudentAnswerTextAttribute()
    {
        if ($this->selected_option_id) {
            return $this->selectedOption->option_text;
        }

        return $this->answer_text;
    }

    public function getCorrectAnswerTextAttribute()
    {
        $question = $this->lectureQuestion;

        switch ($question->type) {
            case 'mcq':
                $correctOption = $question->options()->where('is_correct', true)->first();
                return $correctOption ? $correctOption->option_text : null;

            case 'tf':
            case 'numeric':
            case 'essay':
                return $question->correct_answer;

            default:
                return null;
        }
    }
}
