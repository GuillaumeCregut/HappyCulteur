<?php

namespace App\Service;

use App\Exception\PictureException;
use GdImage;

class PictureFormator
{
    const TO_JPG = 1;
    const TO_PNG = 2;
    const FILE_EXT = [
        self::TO_JPG,
        self::TO_PNG
    ];

    public static function format(string $filename, int $format, int $width, int $height): string |false
    {
        $authorizedExt = ['jpg', 'jpeg', 'png', 'PNG', 'JPG', 'JPEG'];
        if (!file_exists($filename)) {
            throw new PictureException('File not found');
        }
        $ext = pathinfo($filename)['extension'];
        if (!in_array($ext, $authorizedExt, true)) {
            throw new PictureException('Not a valid picture format');
        }

        if (!in_array($format, self::FILE_EXT)) {
            throw new PictureException('Invalid output Format');
        }
        $picture = self::loadFile($filename, $ext);
        $picture = self::formatPicture($picture, $width, $height);
        $result = self::saveFile($picture, $filename, $format);
        return $result;
    }

    private static function loadFile(string $filename, string $ext): GdImage
    {
        $ext = strtolower($ext);
        $result = match ($ext) {
            'jpg', 'jpeg' => imagecreatefromjpeg($filename),
            'png' => imagecreatefrompng($filename),
        };
        if (false === $result) {
            throw new PictureException('Error loading picture');
        }
        return $result;
    }

    private static function formatPicture(GdImage $picture, int $width, int $height): GdImage
    {
        $output = imagecreatetruecolor($width, $height);
        imagealphablending($output, false);
        imagesavealpha($output, true);
        $transparent = imagecolorallocatealpha($output, 0, 0, 0, 127);
        imagefill($output, 0, 0, $transparent);
        imagecopyresampled(
            $output,
            $picture,
            0,
            0,
            0,
            0,
            $width,
            $height,
            imagesx($picture),
            imagesy($picture)
        );
        return $output;
    }
    private static function saveFile(GdImage $picture, string $filename, int $format): string | false
    {
        $infos = pathinfo($filename);
        $path = $infos['dirname'];
        $name = $infos['filename'];
        $result = true;
        $fullPath = $path . DIRECTORY_SEPARATOR . $name;
        switch ($format) {
            case self::TO_JPG:
                $fullPath .= '.jpg';
                $result = imagejpeg($picture, $fullPath);
                break;
            case self::TO_PNG:
                $fullPath .= '.png';
                $result = imagepng($picture, $fullPath);
                break;
        }
        if (!$result) {
            return false;
        }
        if ($filename !== $fullPath) {
            unlink($filename);
        }
        return $fullPath;
    }
}
