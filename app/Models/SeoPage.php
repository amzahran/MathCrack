<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Translatable\HasTranslations;
class SeoPage extends Model
{
    use HasTranslations;
    use HasFactory;

    protected $guarded = [];
    public $translatable = ['title', 'description', 'keywords', 'og_title', 'og_description'];

    protected static function booted()
    {
        static::saved(function ($seoPage) {
            Cache::forget('app_cached_data');
            Cache::forget('seo_pages');
        });

        static::deleted(function ($seoPage) {
            Cache::forget('app_cached_data');
            Cache::forget('seo_pages');
        });
    }

    // الحصول على بيانات SEO للصفحة المطلوبة عن طريق الـ slug
    public static function getBySlug($slug)
    {
        return Cache::remember('seo_page_' . $slug, 3600, function () use ($slug) {
            return self::where('slug', $slug)->first();
        });
    }

    // الحصول على جميع صفحات SEO النشطة
    public static function getAllActive()
    {
        return Cache::remember('seo_pages', 3600, function () {
            return self::get();
        });
    }
}