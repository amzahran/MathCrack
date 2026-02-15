<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LectureAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'lecture_id',
        'time_limit',
        'show_answers',
        'is_active',
    ];

    protected $casts = [
        'show_answers' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function lecture()
    {
        return $this->belongsTo(Lecture::class);
    }

    public function questions()
    {
        return $this->hasMany(LectureQuestion::class)->orderBy('order');
    }

    public function studentAssignments()
    {
        return $this->hasMany(StudentLectureAssignment::class);
    }

    public function getTotalPointsAttribute()
    {
        return $this->questions->sum('points');
    }

    public function getQuestionsCountAttribute()
    {
        return $this->questions->count();
    }

    public function hasTimeLimit()
    {
        return $this->time_limit !== null;
    }
}
