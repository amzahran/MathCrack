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
        Schema::create('test_question_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('test_question_id')->comment('معرف السؤال');
            $table->text('option_text')->comment('نص الخيار');
            $table->string('option_image')->nullable()->comment('صورة الخيار');
            $table->boolean('is_correct')->default(false)->comment('هل الخيار صحيح');
            $table->integer('option_order')->default(1)->comment('ترتيب الخيار');
            $table->timestamps();

            $table->foreign('test_question_id')->references('id')->on('test_questions')->onDelete('cascade');
            $table->index(['test_question_id', 'option_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_question_options');
    }
};
