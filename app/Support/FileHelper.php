<?php

namespace App\Support;

class FileHelper
{
    public static function generateRandomNameFromPath(string $path): string
    {
        $filename = pathinfo($path, PATHINFO_FILENAME);
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        return self::generateRandomName($filename, $extension);
    }

    public static function generateRandomName(string $filename, string $extension): string
    {
        return md5($filename . microtime()) . '.' . $extension;
    }
}
