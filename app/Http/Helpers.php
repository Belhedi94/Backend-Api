<?php
/**
 * Created by PhpStorm.
 * User: rafaa
 * Date: 20/03/2022
 * Time: 10:38
 */

namespace App\Http;

class Helpers
{
    public static function normalizeMobileNumber($mobileNumber) {
        return str_replace([' ', '.', '-', '(', ')', '+'], '', $mobileNumber);
    }

    public static function doesItExist($className, $id) {
        $raw = $className::find($id);
        if (isset($raw))
            return $raw;
    }

    public static function uploadImage($file, $folderName) {
        $filenameWithExt = $file->getClientOriginalName();
        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $fileNameToStore= $filename.'_'.time().'.'.$extension;
        $file->storeAs('public/'.$folderName, $fileNameToStore);

        return $fileNameToStore;
    }

}