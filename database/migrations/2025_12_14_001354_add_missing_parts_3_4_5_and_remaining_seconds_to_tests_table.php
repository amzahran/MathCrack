<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('tests', function (Blueprint $table) {
        $table->integer('part3_questions_count')->nullable()->after('part2_questions_count');
        $table->integer('part3_time_minutes')->nullable()->after('part2_time_minutes');

        $table->integer('part4_questions_count')->nullable()->after('part3_questions_count');
        $table->integer('part4_time_minutes')->nullable()->after('part3_time_minutes');

        $table->integer('part5_questions_count')->nullable()->after('part4_questions_count');
        $table->integer('part5_time_minutes')->nullable()->after('part4_time_minutes');

        $table->integer('remaining_seconds')->nullable()->after('break_time_minutes');
    });
}

public function down(): void
{
    Schema::table('tests', function (Blueprint $table) {
        $table->dropColumn([
            'part3_questions_count',
            'part3_time_minutes',
            'part4_questions_count',
            'part4_time_minutes',
            'part5_questions_count',
            'part5_time_minutes',
            'remaining_seconds',
        ]);
    });
}
};
