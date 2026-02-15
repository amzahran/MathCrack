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
    if (!Schema::hasColumn('invoices', 'course_id')) {
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('course_id')->nullable()->after('type_value');
        });
    }
}

    /**
     * Reverse the migrations.
     */
    public function down()
{
    if (Schema::hasColumn('invoices', 'course_id')) {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('course_id');
        });
    }
}

};
