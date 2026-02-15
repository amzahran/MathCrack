<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lecture_questions', function (Blueprint $table) {
            $table->id();
            $table->text('question_text');
            $table->enum('type', ['mcq', 'tf', 'essay', 'numeric'])->default('mcq');
            $table->unsignedBigInteger('lecture_assignment_id');
            $table->foreign('lecture_assignment_id')->references('id')->on('lecture_assignments')->onDelete('cascade');
            $table->integer('points')->default(1);
            $table->text('correct_answer')->nullable(); // للإجابة الصحيحة
            $table->text('explanation')->nullable(); // شرح الإجابة الصحيحة
            $table->string('question_image')->nullable(); // صورة السؤال
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lecture_questions');
    }
};
