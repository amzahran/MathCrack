<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            DB::table('tests')
                ->whereIn('course_id', function ($query): void {
                    $query->select('id')
                        ->from('courses')
                        ->where(function ($courseQuery): void {
                            $courseQuery
                                ->whereRaw('LOWER(name) LIKE ?', ['%digital sat%'])
                                ->orWhereRaw('LOWER(name) LIKE ?', ['%esti%'])
                                ->orWhere(function ($estQuery): void {
                                    $estQuery
                                        ->whereRaw('LOWER(name) LIKE ?', ['%est i%'])
                                        ->whereRaw('LOWER(name) NOT LIKE ?', ['%est ii%']);
                                });
                        });
                })
                ->where('total_score', '<>', 800)
                ->update(['total_score' => 800]);
        });
    }

    public function down(): void
    {
        // Previous total_score values vary per test and cannot be safely restored automatically.
    }
};
