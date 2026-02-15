<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Support\Str;

class Tax extends Model
{
    use HasTranslations;

    public $translatable = ['name']; // الأعمدة التي تحتاج إلى ترجمة

    protected $guarded = [];

    protected $table = 'taxes';

    // علاقة بين الضرائب والمنتجات
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

}
