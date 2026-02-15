<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('student_tests', function (Blueprint $table) {
        $table->integer('remaining_seconds')->nullable();
    });
}

public function down()
{
    Schema::table('student_tests', function (Blueprint $table) {
        $table->dropColumn('remaining_seconds');
    });
}

};