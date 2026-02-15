<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'order',
        'is_active',
        'test_id', // إذا كانت العلاقة belongsTo
    ];

    /**
     * العلاقة مع الاختبارات
     */
    public function tests(): BelongsToMany
    {
        return $this->belongsToMany(Test::class, 'test_module', 'module_id', 'test_id')
                    ->withTimestamps()
                    ->withPivot(['order']);
    }

    /**
     * العلاقة مع الأسئلة
     */
    public function questions(): HasMany
    {
        return $this->hasMany(TestQuestion::class);
    }
}