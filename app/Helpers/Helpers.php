<?php

use App\Models\Setting;
use Carbon\Carbon;

if(!function_exists('formatDate')){
    function formatDate($date): string{
        $date = Carbon::parse($date)->timezone('Asia/Dhaka');
        $diffInDays = $date->diffInDays(Carbon::now('Asia/Dhaka'));

        if ($diffInDays < 1) {
            return $date->diffForHumans(); 
        }

        return $date->format('M d, Y h:i A');
    }
}

if (!function_exists('getSetting')) {
    function getSetting($key, $default = null)
    {
        $setting = Setting::where('key', $key)->first();
        return $setting == null ? $default : $setting->value;
    }
}