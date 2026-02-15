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
        Schema::create('seo_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique()->comment('الرابط المنفصل للصفحة');
            $table->text('title')->nullable()->comment('عنوان الصفحة - title tag');
            $table->text('description')->nullable()->comment('وصف الصفحة - meta description');
            $table->text('keywords')->nullable()->comment('الكلمات المفتاحية - meta keywords');
            $table->text('og_title')->nullable()->comment('عنوان الصفحة لمشاركات السوشيال ميديا');
            $table->text('og_description')->nullable()->comment('وصف الصفحة لمشاركات السوشيال ميديا');
            $table->string('og_image')->nullable()->comment('الصورة المستخدمة في مشاركات السوشيال ميديا');
            $table->text('structured_data')->nullable()->comment('بيانات منظمة - JSON-LD');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_pages');
    }
};