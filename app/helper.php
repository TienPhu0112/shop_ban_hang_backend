<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

const SLUG_SPLITTER = '-';

if (!function_exists('uploadFileHelper')) {
    function uploadFileHelper($disk, $file) {
        $fileName = Str::slug($file->getClientOriginalName(), SLUG_SPLITTER) . Carbon::now()->timestamp;
        $fileExt = $file->getClientOriginalExtension();
        $name = $file->storeAs('', $fileName . '.' . $fileExt, $disk);

        return Config::get('filesystems.disks.' . $disk . '.path') . '/' . $name;
    }
}

if (!function_exists('getFullPublicFileUrl')) {
    function getFullPublicFileUrl($path) {
        if ($path) {
            return env('APP_URL') . $path;
        }

        return false;
    }
}
