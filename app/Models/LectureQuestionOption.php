<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LectureQuestionOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'lecture_question_id',
        'option_text',
        'option_image',
        'is_correct',
        'order',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function lectureQuestion()
    {
        return $this->belongsTo(LectureQuestion::class);
    }

    public function studentAnswers()
    {
        return $this->hasMany(StudentLectureAnswer::class, 'selected_option_id');
    }
}
