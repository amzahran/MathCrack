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
        Schema::create('student_test_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_test_id')->comment('معرف اختبار الطالب');
            $table->unsignedBigInteger('test_question_id')->comment('معرف السؤال');
            $table->unsignedBigInteger('selected_option_id')->nullable()->comment('معرف الخيار المختار للأسئلة متعددة الخيارات');
            $table->text('answer_text')->nullable()->comment('نص الإجابة للأسئلة الرقمية أو النصية');
            $table->boolean('is_correct')->nullable()->comment('هل الإجابة صحيحة');
            $table->decimal('score_earned', 8, 2)->default(0)->comment('الدرجة المكتسبة من السؤال');
            $table->timestamp('answered_at')->nullable()->comment('وقت الإجابة على السؤال');
            $table->timestamps();

            $table->foreign('student_test_id')->references('id')->on('student_tests')->onDelete('cascade');
            $table->foreign('test_question_id')->references('id')->on('test_questions')->onDelete('cascade');
            $table->foreign('selected_option_id')->references('id')->on('test_question_options')->onDelete('set null');

            $table->unique(['student_test_id', 'test_question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_test_answers');
    }
};
