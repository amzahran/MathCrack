<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            DB::table('tests')
                ->where('price', '>', 0)
                ->where('price', '<>', 50.00)
                ->update(['price' => 50.00]);
        });
    }

    public function down(): void
    {
        // Previous paid test prices vary and cannot be safely restored automatically.
    }
};
