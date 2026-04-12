<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->integer('module1_easy_score')->default(0)->after('initial_score');
            $table->integer('module1_medium_score')->default(0)->after('module1_easy_score');
            $table->integer('module1_hard_score')->default(0)->after('module1_medium_score');

            $table->integer('module2_easy_score')->default(0)->after('module1_hard_score');
            $table->integer('module2_medium_score')->default(0)->after('module2_easy_score');
            $table->integer('module2_hard_score')->default(0)->after('module2_medium_score');

            $table->integer('module3_easy_score')->default(0)->after('module2_hard_score');
            $table->integer('module3_medium_score')->default(0)->after('module3_easy_score');
            $table->integer('module3_hard_score')->default(0)->after('module3_medium_score');

            $table->integer('module4_easy_score')->default(0)->after('module3_hard_score');
            $table->integer('module4_medium_score')->default(0)->after('module4_easy_score');
            $table->integer('module4_hard_score')->default(0)->after('module4_medium_score');

            $table->integer('module5_easy_score')->default(0)->after('module4_hard_score');
            $table->integer('module5_medium_score')->default(0)->after('module5_easy_score');
            $table->integer('module5_hard_score')->default(0)->after('module5_medium_score');
        });
    }

    public function down(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->dropColumn([
                'module1_easy_score',
                'module1_medium_score',
                'module1_hard_score',

                'module2_easy_score',
                'module2_medium_score',
                'module2_hard_score',

                'module3_easy_score',
                'module3_medium_score',
                'module3_hard_score',

                'module4_easy_score',
                'module4_medium_score',
                'module4_hard_score',

                'module5_easy_score',
                'module5_medium_score',
                'module5_hard_score',
            ]);
        });
    }
};