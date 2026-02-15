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

            if (!Schema::hasColumn('student_tests', 'part1_started_at')) {
                $table->timestamp('part1_started_at')->nullable()->after('current_module_started_at');
            }

            if (Schema::hasColumn('student_tests', 'remaining_seconds')) {
                $table->dropColumn('remaining_seconds');
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_tests', function (Blueprint $table) {

            if (Schema::hasColumn('student_tests', 'part1_started_at')) {
                $table->dropColumn('part1_started_at');
            }

            if (Schema::hasColumn('student_tests', 'current_module_started_at')) {
                $table->dropColumn('current_module_started_at');
            }

            if (Schema::hasColumn('student_tests', 'current_module')) {
                $table->dropColumn('current_module');
            }
        });
    }
};
