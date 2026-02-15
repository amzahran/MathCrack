<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockedIp extends Model
{
    protected $table = 'firewall_ips';
    protected $guarded = [];

    public function scopeBlocked($query, $ip)
    {
        return $query->where('ip', $ip)->where('blocked', 1);
    }
}
