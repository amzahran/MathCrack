<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $guarded = [];

    public function active()
    {
        return $this->where('is_active', true);
    }

    public function default()
    {
        return $this->where('is_default', true)->first();
    }
}
