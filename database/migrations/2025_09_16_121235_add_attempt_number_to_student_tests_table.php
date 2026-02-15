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
        Schema::table('student_tests', function (Blueprint $table) {
            $table->integer('attempt_number')->default(1)->after('test_id')->comment('رقم المحاولة');
            $table->index(['student_id', 'test_id', 'attempt_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_tests', function (Blueprint $table) {
            $table->dropIndex(['student_id', 'test_id', 'attempt_number']);
            $table->dropColumn('attempt_number');
        });
    }
};
