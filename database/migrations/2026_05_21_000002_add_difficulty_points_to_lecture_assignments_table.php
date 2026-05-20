<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lecture_assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('lecture_assignments', 'easy_points')) {
                $table->integer('easy_points')->nullable()->default(1)->after('time_limit');
            }

            if (!Schema::hasColumn('lecture_assignments', 'medium_points')) {
                $table->integer('medium_points')->nullable()->default(2)->after('easy_points');
            }

            if (!Schema::hasColumn('lecture_assignments', 'hard_points')) {
                $table->integer('hard_points')->nullable()->default(3)->after('medium_points');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lecture_assignments', function (Blueprint $table) {
            if (Schema::hasColumn('lecture_assignments', 'hard_points')) {
                $table->dropColumn('hard_points');
            }

            if (Schema::hasColumn('lecture_assignments', 'medium_points')) {
                $table->dropColumn('medium_points');
            }

            if (Schema::hasColumn('lecture_assignments', 'easy_points')) {
                $table->dropColumn('easy_points');
            }
        });
    }
};
