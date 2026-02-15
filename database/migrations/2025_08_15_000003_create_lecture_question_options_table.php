<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lecture_question_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lecture_question_id');
            $table->foreign('lecture_question_id')->references('id')->on('lecture_questions')->onDelete('cascade');
            $table->string('option_text');
            $table->string('option_image')->nullable(); // صورة الخيار
            $table->boolean('is_correct')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lecture_question_options');
    }
};
