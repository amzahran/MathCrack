<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            if (!Schema::hasColumn('tests', 'modules_count')) {
                $table->unsignedInteger('modules_count')->nullable()->after('default_question_score');
            }

            if (!Schema::hasColumn('tests', 'modules_data')) {
                $table->json('modules_data')->nullable()->after('modules_count');
            }

            if (!Schema::hasColumn('tests', 'break_time_minutes')) {
                $table->unsignedInteger('break_time_minutes')->nullable()->after('modules_data');
            }

            if (!Schema::hasColumn('tests', 'max_attempts')) {
                $table->unsignedInteger('max_attempts')->nullable()->default(1)->after('break_time_minutes');
            }

            if (!Schema::hasColumn('tests', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('max_attempts');
            }

            // الأعمدة القديمة لو ناقصة في جدولك
            if (!Schema::hasColumn('tests', 'part1_questions_count')) {
                $table->unsignedInteger('part1_questions_count')->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('tests', 'part1_time_minutes')) {
                $table->unsignedInteger('part1_time_minutes')->nullable()->after('part1_questions_count');
            }
            if (!Schema::hasColumn('tests', 'part2_questions_count')) {
                $table->unsignedInteger('part2_questions_count')->nullable()->after('part1_time_minutes');
            }
            if (!Schema::hasColumn('tests', 'part2_time_minutes')) {
                $table->unsignedInteger('part2_time_minutes')->nullable()->after('part2_questions_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->dropColumn([
                'modules_count',
                'modules_data',
                'break_time_minutes',
                'max_attempts',
                'is_active',
                'part1_questions_count',
                'part1_time_minutes',
                'part2_questions_count',
                'part2_time_minutes',
            ]);
        });
    }
};
