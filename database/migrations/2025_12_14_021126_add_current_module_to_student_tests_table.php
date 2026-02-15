<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_tests', function (Blueprint $table) {

            if (!Schema::hasColumn('student_tests', 'current_module')) {
                $table->unsignedTinyInteger('current_module')->default(1)->after('current_score');
            }

            if (!Schema::hasColumn('student_tests', 'current_module_started_at')) {
                $table->timestamp('current_module_started_at')->nullable()->after('current_module');
            }

            $columns = [
                'break_started_at' => 'timestamp',
                'part2_started_at' => 'timestamp',
                'submitted_at'     => 'timestamp',
                'final_score'      => 'decimal',
                'is_paused'        => 'boolean',
            ];

            foreach ($columns as $column => $type) {

                if (Schema::hasColumn('student_tests', $column)) {
                    continue;
                }

                if ($type === 'boolean') {
                    $table->boolean($column)->default(false);
                } elseif ($type === 'decimal') {
                    $table->decimal($column, 8, 2)->nullable();
                } else {
                    $table->timestamp($column)->nullable();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_tests', function (Blueprint $table) {

            $drop = [
                'break_started_at',
                'part2_started_at',
                'submitted_at',
                'final_score',
                'is_paused',
                'current_module_started_at',
                'current_module',
            ];

            foreach ($drop as $col) {
                if (Schema::hasColumn('student_tests', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
