<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

const SLUG_SPLITTER = '-';

if (!function_exists('uploadFileHelper')) {
    function uploadFileHelper($disk, $file) {
        $fileName = Str::slug($file->getClientOriginalName(), SLUG_SPLITTER) . Carbon::now()->timestamp;
        $fileExt = $file->getClientOriginalExtension();
        $path = $file->storeAs('', $fileName . '.' . $fileExt, $disk);

        return $path;
    }
}
