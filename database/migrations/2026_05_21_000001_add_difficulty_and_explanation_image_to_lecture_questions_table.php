<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lecture_questions', function (Blueprint $table) {
            if (!Schema::hasColumn('lecture_questions', 'difficulty')) {
                $table->string('difficulty')->nullable()->after('points');
            }

            if (!Schema::hasColumn('lecture_questions', 'explanation_image')) {
                $table->string('explanation_image')->nullable()->after('explanation');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lecture_questions', function (Blueprint $table) {
            if (Schema::hasColumn('lecture_questions', 'explanation_image')) {
                $table->dropColumn('explanation_image');
            }

            if (Schema::hasColumn('lecture_questions', 'difficulty')) {
                $table->dropColumn('difficulty');
            }
        });
    }
};
