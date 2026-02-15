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
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            //
        });
    }
};


Schema::table('tests', function (Blueprint $table) {
    $table->integer('part3_questions_count')->nullable();
    $table->integer('part4_questions_count')->nullable();
    $table->integer('part5_questions_count')->nullable();

    $table->integer('part3_time_minutes')->nullable();
    $table->integer('part4_time_minutes')->nullable();
    $table->integer('part5_time_minutes')->nullable();

    $table->integer('modules_count')->nullable();
    $table->longText('modules_data')->nullable();

    $table->integer('remaining_seconds')->nullable();
    $table->integer('break_time_minutes')->nullable();
});
