<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentLectureAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'lecture_assignment_id',
        'started_at',
        'submitted_at',
        'score',
        'total_points',
        'percentage',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'percentage' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function lectureAssignment()
    {
        return $this->belongsTo(LectureAssignment::class);
    }

    public function answers()
    {
        return $this->hasMany(StudentLectureAnswer::class);
    }

    public function getTimeSpentAttribute()
    {
        if (!$this->started_at || !$this->submitted_at) {
            return null;
        }

        // حساب الوقت المستغرق بالدقائق
        $timeSpent = $this->submitted_at->diffInMinutes($this->started_at);

        // التأكد من أن الوقت موجب
        return max(0, $timeSpent);
    }

    public function isTimeExpired()
    {
        if (!$this->lectureAssignment->time_limit || !$this->started_at) {
            return false;
        }

        $timeLimit = $this->lectureAssignment->time_limit; // بالدقائق

        // حساب الوقت المنقضي بالدقائق منذ بداية الاختبار
        $elapsedMinutes = now()->diffInMinutes($this->started_at);

        return $elapsedMinutes >= $timeLimit;
    }

    public function calculateScore()
    {
        $totalPoints = 0;
        $earnedPoints = 0;

        foreach ($this->answers as $answer) {
            $totalPoints += $answer->lectureQuestion->points;
            if ($answer->is_correct) {
                $earnedPoints += $answer->points_earned;
            }
        }

        $this->score = $earnedPoints;
        $this->total_points = $totalPoints;
        $this->percentage = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 2) : 0.00;
        $this->save();

        return [
            'score' => $earnedPoints,
            'total_points' => $totalPoints,
            'percentage' => $this->percentage
        ];
    }
}
