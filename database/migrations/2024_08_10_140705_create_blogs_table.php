<?php

// database/migrations/xxxx_xx_xx_create_blogs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogsTable extends Migration
{
    public function up()
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->text('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('title'); // عنوان المقال
            $table->string('slug')->unique(); // حقل للـ slug
            $table->longText('content'); // المحتوى النصي للمقال
            $table->string('image')->nullable(); // صورة المقال
            $table->integer('views')->default(0); // عدد المشاهدات
            $table->enum('status', ['published', 'draft'])->default('published'); // حالة المقال
            $table->unsignedBigInteger('blog_category_id')->nullable(); // ربط الفئة
            $table->foreign('blog_category_id')->references('id')->on('blog_categories')->onDelete('set null');
            $table->unsignedBigInteger('user_id')->nullable(); // ربط المستخدم
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('blogs');
    }
}
