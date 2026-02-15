<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $guarded = [];

    public function getFlagUrlAttribute()
    {
        // الطريقة الأولى: استخدام Flagicons من CountryFlags
        return "https://flagicons.lipis.dev/flags/4x3/{$this->flag}.svg";

        // الطريقة الثانية: استخدام FlagCDN
        // return "https://flagcdn.com/w80/{$this->flag}.png";
    }

    public function getFlagHtmlAttribute()
    {
        return "<span class='flag-icon flag-icon-{$this->flag}'></span>";
    }
}