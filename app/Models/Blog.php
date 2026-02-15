<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Support\Str;

class Blog extends Model
{
    use HasTranslations;

    public $translatable = ['title', 'content', 'meta_keywords', 'meta_description']; // الأعمدة التي تحتاج إلى ترجمة

    protected $guarded = [];

    /**
     * إنشاء سلاج فريد
     * @param string $title
     * @param int|null $ignoreId
     * @return string
     */
    public static function createUniqueSlug($title, $ignoreId = null)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        $query = static::where('slug', $slug);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        while ($query->exists()) {
            $slug = $originalSlug . '-' . $count;
            $query = static::where('slug', $slug);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }
            $count++;
        }

        return $slug;
    }

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function comments()
    {
        return $this->hasMany(BlogComment::class)->whereNull('parent_id');
    }

    public function commentsCount()
    {
        return $this->hasMany(BlogComment::class)->count();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
