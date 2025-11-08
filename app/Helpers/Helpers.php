<?php

use App\Models\Store;
use App\Models\Setting;

if(!function_exists('storeCode')){
    function storeCode()
    {
        $store = Store::query()->where('id', request()->header('store'))->first();
        if($store){
            return $store->code;
        }else{
            return null;
        } 
    }
}

if (!function_exists('getSetting')) {
    function getSetting($key, $default = null)
    {
        $setting = Setting::where('key', $key)->first();
        return $setting == null ? $default : $setting->value;
    }
}