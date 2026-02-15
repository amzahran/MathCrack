<?php

// database/migrations/xxxx_xx_xx_create_blog_comments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogCommentsTable extends Migration
{
    public function up()
    {
        Schema::create('blog_comments', function (Blueprint $table) {
            $table->id();
            $table->text('content'); // محتوى التعليق
            $table->unsignedBigInteger('user_id')->nullable(); // ربط المستخدم
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('blog_id')->nullable(); // ربط المقال
            $table->foreign('blog_id')->references('id')->on('blogs')->onDelete('cascade');
            $table->unsignedBigInteger('parent_id')->nullable(); // ربط التعليق الأب
            $table->foreign('parent_id')->references('id')->on('blog_comments')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('blog_comments');
    }
}
