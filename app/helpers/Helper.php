<?php

namespace App\Helper;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;

class Helper {
    
    const SLUG_SPLITTER = '-';

    public static function uploadFileHelper($disk, $file) {
        $fileName = Str::slug($file->getClientOriginalName(), self::SLUG_SPLITTER) . Carbon::now()->timestamp;
        $fileExt = $file->getClientOriginalExtension();
        $name = $file->storeAs('', $fileName . '.' . $fileExt, $disk);

        return Config::get('filesystems.disks.' . $disk . '.path') . '/' . $name;
    }

    public static function getFullPublicFileUrl($path) {
        if ($path) {
            return env('APP_URL') . $path;
        }

        return false;
    }
}
