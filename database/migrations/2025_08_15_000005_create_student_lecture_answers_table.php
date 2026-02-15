<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('student_lecture_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_lecture_assignment_id');
            $table->foreign('student_lecture_assignment_id')->references('id')->on('student_lecture_assignments')->onDelete('cascade');
            $table->unsignedBigInteger('lecture_question_id');
            $table->foreign('lecture_question_id')->references('id')->on('lecture_questions')->onDelete('cascade');
            $table->text('answer_text')->nullable();
            $table->unsignedBigInteger('selected_option_id')->nullable();
            $table->foreign('selected_option_id')->references('id')->on('lecture_question_options')->onDelete('cascade');
            $table->boolean('is_correct')->nullable();
            $table->integer('points_earned')->default(0);
            $table->text('teacher_feedback')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_lecture_answers');
    }
};
