<?php

if (!function_exists('theme')) {
    function theme($view)
    {
        $theme = app('theme');
        return "themes.{$theme}.{$view}";
    }
}