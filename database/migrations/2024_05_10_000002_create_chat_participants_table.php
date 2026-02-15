<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatParticipantsTable extends Migration
{
    public function up()
    {
        Schema::create('chat_participants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chat_id');
            $table->unsignedBigInteger('user_id');
            $table->boolean('is_admin')->default(false); // هل المستخدم مدير للمجموعة
            $table->timestamp('last_read_at')->nullable(); // وقت آخر قراءة للرسائل
            $table->timestamps();

            $table->foreign('chat_id')->references('id')->on('chats')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['chat_id', 'user_id']); // لا يمكن للمستخدم الانضمام للمحادثة مرتين
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_participants');
    }
}