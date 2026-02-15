<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Support\Str;

class BlogCategory extends Model
{
    use HasTranslations;

    public $translatable = ['name', 'meta_keywords', 'meta_description']; // الأعمدة التي تحتاج إلى ترجمة

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

    // العلاقة مع الفئات الفرعية
    public function children()
    {
        return $this->hasMany(BlogCategory::class, 'parent_id');
    }

    // العلاقة مع الفئة الأب
    public function parent()
    {
        return $this->belongsTo(BlogCategory::class, 'parent_id');
    }

    public function blogs()
    {
        return $this->hasMany(Blog::class);
    }
}
