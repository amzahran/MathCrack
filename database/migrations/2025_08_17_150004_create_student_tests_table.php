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
        Schema::create('student_tests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->comment('معرف الطالب');
            $table->unsignedBigInteger('test_id')->comment('معرف الاختبار');

            // حالة التقدم
            $table->enum('status', ['not_started', 'part1_in_progress', 'break_time', 'part2_in_progress', 'completed'])->default('not_started')->comment('حالة الاختبار');
            $table->decimal('current_score', 8, 2)->default(200)->comment('الدرجة الحالية');
            $table->decimal('final_score', 8, 2)->nullable()->comment('الدرجة النهائية');

            // أوقات الاختبار
            $table->timestamp('started_at')->nullable()->comment('وقت بداية الاختبار');
            $table->timestamp('part1_started_at')->nullable()->comment('وقت بداية الجزء الأول');
            $table->timestamp('part1_ended_at')->nullable()->comment('وقت انتهاء الجزء الأول');
            $table->timestamp('break_started_at')->nullable()->comment('وقت بداية الاستراحة');
            $table->timestamp('part2_started_at')->nullable()->comment('وقت بداية الجزء الثاني');
            $table->timestamp('completed_at')->nullable()->comment('وقت انتهاء الاختبار');

            $table->integer('time_spent_part1')->nullable()->comment('الوقت المستغرق في الجزء الأول بالثواني');
            $table->integer('time_spent_part2')->nullable()->comment('الوقت المستغرق في الجزء الثاني بالثواني');

            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('test_id')->references('id')->on('tests')->onDelete('cascade');
            $table->unique(['student_id', 'test_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_tests');
    }
};
