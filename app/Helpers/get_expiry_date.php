<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

if (!function_exists('get_expiry_date')) {
    function get_expiry_date()
    {
        $key = DB::table('settings')->where('option', 'key')->value('value');

        if (strpos($key, '.') === false) {
            return 'NAN';
        }

        [$encoded, $signature] = explode('.', $key, 2);
        $expectedSig = hash_hmac('sha256', $encoded, config('app.secretCode'));

        if (!hash_equals($expectedSig, $signature)) {
            return 'NAN';
        }

        $data = json_decode(base64_decode($encoded), true);

        if (!is_array($data) || !isset($data['expiry'])) {
            return 'NAN';
        }

        if (Carbon::parse($data['expiry'])->greaterThan(Carbon::now()->addYears(100))) {
            return 'life time';
        } else {
            return $data['expiry'];
        }

    }
}