<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('test_questions', function (Blueprint $table) {
            if (!Schema::hasColumn('test_questions', 'module_number')) {
                $table->integer('module_number')->default(1)->after('updated_at');
            } else {
                $table->integer('module_number')->nullable()->change();
            }

            if (Schema::hasColumn('test_questions', 'part')) {
                $table->string('part', 20)->nullable()->change();
            }
        });

        sleep(1);

        if (Schema::hasColumn('test_questions', 'part')) {
            DB::table('test_questions')
                ->whereNotNull('part')
                ->where(function ($query) {
                    $query->whereNull('module_number')
                          ->orWhere('module_number', 0);
                })
                ->update([
                    'module_number' => DB::raw("
                        CASE
                            WHEN part = 'part1' THEN 1
                            WHEN part = 'part2' THEN 2
                            WHEN part = 'part3' THEN 3
                            WHEN part = 'part4' THEN 4
                            WHEN part = 'part5' THEN 5
                            ELSE 1
                        END
                    "),
                ]);
        }

        DB::table('test_questions')
            ->whereNull('module_number')
            ->orWhere('module_number', 0)
            ->update(['module_number' => 1]);

        Schema::table('test_questions', function (Blueprint $table) {
            if (Schema::hasColumn('test_questions', 'module_number')) {
                $table->integer('module_number')->nullable(false)->default(1)->change();

                $table->index('module_number');
                $table->index(['test_id', 'module_number']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('test_questions', function (Blueprint $table) {
            if (Schema::hasColumn('test_questions', 'part')) {
                $table->string('part', 20)->nullable()->change();
            }

            // لا نحذف module_number حفاظاً على البيانات
            // لا نحذف indexes لتفادي أخطاء drop في بيئات مختلفة
        });
    }
};
