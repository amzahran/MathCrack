<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tests', function (Blueprint $table) {

            $addIntNotNull0 = function (string $col) use ($table) {
                if (!Schema::hasColumn('tests', $col)) {
                    $table->integer($col)->default(0);
                }
            };

            $addTimestampNullable = function (string $col) use ($table) {
                if (!Schema::hasColumn('tests', $col)) {
                    $table->timestamp($col)->nullable();
                }
            };

            $colsInt = [
                'part3_questions_count',
                'part4_questions_count',
                'part5_questions_count',
            ];

            foreach ($colsInt as $c) {
                $addIntNotNull0($c);
            }

            $colsTime = [
                'part3_time_seconds',
                'part4_time_seconds',
                'part5_time_seconds',
            ];

            foreach ($colsTime as $c) {
                $addIntNotNull0($c);
            }

            $colsTs = [
                'part3_started_at',
                'part3_finished_at',
                'part4_started_at',
                'part4_finished_at',
                'part5_started_at',
                'part5_finished_at',
            ];

            foreach ($colsTs as $c) {
                $addTimestampNullable($c);
            }
        });
    }

    public function down(): void
    {
        Schema::table('tests', function (Blueprint $table) {

            $cols = [
                'part3_questions_count',
                'part4_questions_count',
                'part5_questions_count',
                'part3_time_seconds',
                'part4_time_seconds',
                'part5_time_seconds',
                'part3_started_at',
                'part3_finished_at',
                'part4_started_at',
                'part4_finished_at',
                'part5_started_at',
                'part5_finished_at',
            ];

            foreach ($cols as $c) {
                if (Schema::hasColumn('tests', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};
