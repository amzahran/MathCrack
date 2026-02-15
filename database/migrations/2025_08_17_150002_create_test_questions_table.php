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
        Schema::create('test_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('test_id')->comment('معرف الاختبار');
            $table->text('question_text')->comment('نص السؤال');
            $table->string('question_image')->nullable()->comment('صورة السؤال');
            $table->enum('type', ['mcq', 'tf', 'numeric'])->comment('نوع السؤال: اختيار متعدد، صح/خطأ، رقمي');
            $table->enum('part', ['part1', 'part2'])->comment('جزء السؤال في الاختبار');
            $table->integer('question_order')->comment('ترتيب السؤال في الجزء');
            $table->integer('score')->default(15)->comment('درجة السؤال');
            $table->text('correct_answer')->comment('الإجابة الصحيحة');
            $table->timestamps();

            $table->foreign('test_id')->references('id')->on('tests')->onDelete('cascade');
            $table->index(['test_id', 'part', 'question_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_questions');
    }
};
