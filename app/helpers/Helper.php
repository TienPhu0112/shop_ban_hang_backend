<?php

namespace App\Helper;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Helper
{

    const SLUG_SPLITTER = '-';

    public static function uploadFileHelper($disk, $file, $subPath = '')
    {
        $fileName = Str::slug($file->getClientOriginalName(), self::SLUG_SPLITTER) . Carbon::now()->timestamp;
        $fileExt = $file->getClientOriginalExtension();
        $name = $file->storeAs($subPath, $fileName . '.' . $fileExt, $disk);

        return Config::get('filesystems.disks.' . $disk . '.path') . '/' . $name;
    }

    public static function getFullPublicFileUrl($path)
    {
        if ($path) {
            return env('APP_URL') . $path;
        }

        return false;
    }

    public static function deleteFileByDisk($disk, $fileName, $subPath = '')
    {
        $filePath = $subPath === ''
            ? $fileName
            : $subPath . DIRECTORY_SEPARATOR . preg_replace('/.+\//', '', $fileName);

        Storage::disk($disk)->delete($filePath);
    }

    public static function deleteFileByFullPathHelper($filePath)
    {
        $productImage = str_replace('/storage', '', $filePath);
        Storage::delete('/public' . $productImage);
    }

    public static function deleteMultipleFiles(array | object $filePaths)
    {
        foreach ($filePaths as $path) {
            $productImage = str_replace('/storage', '', $path);
            Storage::delete('/public' . $productImage);
        }
    }

    public static function destroyMultipleRecord(Collection $records)
    {
        $count = 0;
        try {
            DB::beginTransaction();
            foreach ($records as $record) {
                if ($record->delete()) {
                    $count++;
                }
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return $count;
    }
}
