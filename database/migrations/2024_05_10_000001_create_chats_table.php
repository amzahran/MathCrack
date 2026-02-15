<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatsTable extends Migration
{
    public function up()
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // اسم المحادثة (يستخدم للمجموعات)
            $table->boolean('is_group')->default(false); // هل هي محادثة جماعية أم فردية
            $table->text('description')->nullable(); // وصف للمحادثة الجماعية
            $table->unsignedBigInteger('created_by'); // المستخدم الذي أنشأ المحادثة
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chats');
    }
}