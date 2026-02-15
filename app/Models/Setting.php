<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'option',
        'value',
    ];
    public $timestamps = false;

    protected static function booted()
    {
        static::saved(function ($setting) {
            Cache::forget('app_cached_data');
        });

        static::deleted(function ($setting) {
            Cache::forget('app_cached_data');
        });
    }
}
