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
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('اسم الاختبار');
            $table->text('description')->nullable()->comment('وصف الاختبار');
            $table->unsignedBigInteger('course_id')->comment('معرف الكورس');
            $table->decimal('price', 8, 2)->default(0)->comment('سعر الاختبار المنفرد');
            $table->integer('total_score')->default(800)->comment('الدرجة الكلية للاختبار');
            $table->integer('initial_score')->default(200)->comment('الدرجة الأولية للطالب');
            $table->integer('default_question_score')->default(15)->comment('درجة السؤال الافتراضية');

            // تفاصيل الأجزاء
            $table->integer('part1_questions_count')->comment('عدد أسئلة الجزء الأول');
            $table->integer('part1_time_minutes')->comment('وقت الجزء الأول بالدقائق');
            $table->integer('part2_questions_count')->comment('عدد أسئلة الجزء الثاني');
            $table->integer('part2_time_minutes')->comment('وقت الجزء الثاني بالدقائق');
            $table->integer('break_time_minutes')->default(15)->comment('وقت الاستراحة بالدقائق');

            $table->boolean('is_active')->default(true)->comment('حالة تفعيل الاختبار');
            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->unique(['name', 'course_id'], 'unique_test_name_per_course');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tests');
    }
};
