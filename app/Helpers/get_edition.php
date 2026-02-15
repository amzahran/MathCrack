<?php

use Illuminate\Support\Facades\DB;

if (!function_exists('get_edition')) {
    function get_edition()
    {
        $signed = DB::table('settings')->where('option', 'app_edition')->value('value');

        if (!$signed) return null;

        $data = json_decode(base64_decode($signed), true);

        if (!isset($data['edition'], $data['sig'])) return null;

        $expected = hash_hmac('sha256', $data['edition'], config('app.secretCode'));

        if (!hash_equals($expected, $data['sig'])) return null;

        return $data['edition'];
    }
}