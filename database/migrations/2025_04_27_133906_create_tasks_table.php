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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['new', 'in_progress', 'completed', 'delayed', 'cancelled'])->default('new');
            $table->unsignedBigInteger('assigned_to')->nullable(); // user id
            $table->unsignedBigInteger('created_by'); // admin or user who created
            $table->dateTime('due_date')->nullable();
            $table->dateTime('completed_at')->nullable(); // تسجيل وقت إتمام المهمة
            $table->timestamps();

            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
