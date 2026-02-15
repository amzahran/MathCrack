<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
class Question extends Model
{
    use HasTranslations;
    protected $guarded  = [];
    public $translatable = ['question', 'answer'];
}
