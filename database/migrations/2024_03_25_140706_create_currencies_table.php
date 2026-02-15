<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrenciesTable extends Migration
{
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // كود العملة (مثل USD, EUR)
            $table->string('name')->nullable(); // اسم العملة
            $table->string('symbol')->nullable(); // رمز العملة
            $table->decimal('rate', 50, 8); // معدل التحويل
            $table->boolean('is_manual')->default(false); // تحديث يدوي أو تلقائي
            $table->boolean('is_active')->default(false); // العملة الافتراضية
            $table->timestamp('last_updated_at')->nullable(); // آخر وقت تحديث
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('currencies');
    }
}
