<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogComment extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }

    public function parent()
    {
        return $this->belongsTo(BlogComment::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(BlogComment::class, 'parent_id');
    }

    public function allReplies()
    {
        return $this->hasMany(BlogComment::class, 'parent_id')->with('allReplies');
    }

    public function getAllRepliesCountAttribute()
    {
        return $this->allReplies()->count() + $this->allReplies->sum(function ($reply) {
            return $reply->getAllRepliesCountAttribute();
        });
    }
}
