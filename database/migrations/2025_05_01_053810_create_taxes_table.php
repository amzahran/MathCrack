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
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->text('name'); // مثل VAT أو Sales Tax
            $table->enum('type', ['fixed', 'percentage']);
            $table->decimal('rate', 11, 2); // نسبة الضريبة، مثلاً 15.00
            $table->boolean('is_default')->default(false); // تطبق تلقائيًا؟
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxes');
    }
};
