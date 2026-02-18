<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Lecture;
use App\Models\Level;
use App\Models\Test;
use Illuminate\Support\Facades\DB;

class PlatformStatsService
{
    public function dashboard(): array
    {
        return [
            'levels'   => Level::count(),
            'courses'  => Course::count(),
            'lectures' => Lecture::count(),
            'tests'    => Test::count(),
        ];
    }

    public function front(): array
    {
        return [
            'satisfied_students'  => $this->studentsCount(),
            'total_courses'       => Course::count(),
            'Practice_Tests'      => Test::count(),
            'expert_instructors'  => $this->instructorsCount(),
        ];
    }

    private function studentsCount(): int
    {
        if ($this->tableExists('students')) {
            return (int) DB::table('students')->count();
        }

        return (int) DB::table('users')->count();
    }

    private function instructorsCount(): int
    {
        if ($this->tableExists('instructors')) {
            return (int) DB::table('instructors')->count();
        }

        return 1;
    }

    private function tableExists(string $table): bool
    {
        try {
            return DB::getSchemaBuilder()->hasTable($table);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
