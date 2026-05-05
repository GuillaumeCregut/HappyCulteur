<?php

namespace App\Tool;

class PathMaker
{
    public static function makeDataloggerConfigPath(int $userId, int $hiveId, string $basePath): string
    {
        $fullPath = $basePath . $userId . DIRECTORY_SEPARATOR;
        $fullPath .= 'datalogger' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR;
        $fullPath .= $hiveId . DIRECTORY_SEPARATOR;
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0766, true);
        }
        return $fullPath;
    }

    public static function makeDataloggerFilePath(int $userId, int $hiveId, string $basePath): string
    {
        $fullPath = $basePath . $userId . DIRECTORY_SEPARATOR;
         $fullPath .= 'datalogger' . DIRECTORY_SEPARATOR . 'datas' . DIRECTORY_SEPARATOR;
         $fullPath .= $hiveId . DIRECTORY_SEPARATOR;
         return $fullPath;
    }
}