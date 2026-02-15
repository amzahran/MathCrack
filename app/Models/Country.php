<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $guarded = [];

    /**
     * علاقة البلد بالمدن - علاقة واحد لكثير
     * Get the cities for the country.
     */
    // public function cities()
    // {
    //     return $this->hasMany(City::class);
    // }
}