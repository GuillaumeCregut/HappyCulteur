<?php

namespace App\Service;

use GdImage;
use RuntimeException;

class HivePictureCreator
{

    const B_AND_WHITE = 1;
    const DEAD = 2;
    
    private GdImage $picture;

    public function __construct(private string $deadFile)
    {
       
    }

    public function convert(
        string $filename,
        string $destinationPath,
        int $mode
    ): bool {
        $this->picture = $this->factory($filename);
        $this->reduce();
        if ($mode >= self::B_AND_WHITE) {
            if(! $this->removeColor()) {
            throw new RuntimeException('Converting color failed');
            }
        }
        if ($mode === self::DEAD) {
            $this->dead();
        }
        return $this->save($destinationPath);
    }

    private function save(string $destination): bool
    {
        return imagepng($this->picture, $destination);
    }

    private function reduce(): void
    {
        $width = imagesx($this->picture);
        if ($width <= 120) {
            return;
        }
        $newPicture = imagescale($this->picture, 120);
        if (!$newPicture) {
            throw new RuntimeException('Failed resizing picture');
        }
        $this->picture = $newPicture;
    }

    private function removeColor(): bool
    {
        return imagefilter($this->picture, IMG_FILTER_GRAYSCALE);
    }

     private function dead(): void
    {
        $deadPicture = imagecreatefrompng($this->deadFile);
        if(! $deadPicture) {
            throw new RuntimeException('Unable to load resource picture');
        }
        $deadPicture = imagescale($deadPicture, 60);
        imagesavealpha($deadPicture, true);
        imagealphablending($deadPicture, false);
        imagealphablending($this->picture, true);
        $width = imagesx($deadPicture);
        $height = imagesy($deadPicture);
        $destWidth = imagesx($this->picture);
        $destHeight = imagesy($this->picture);
        $startX = ($destWidth - $width) / 2;
        $startY = ($destHeight - $height) / 2;
        imagecopy($this->picture, $deadPicture, $startX, $startY, 0, 0, $width, $height);
    }

    private function factory(string $filename): GdImage
    {
        $info = getimagesize($filename);
        switch ($info[2]) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($filename);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($filename);
                break;
            default:
                throw new RuntimeException('Unsupported format');
        }
        if(! $image) {
            throw new RuntimeException('Failed to load original picture');
        }
        $width = imagesx($image);
        $height = imagesy($image);
        $newImage = imagecreatetruecolor($width, $height);
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        $transparent = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
        imagefill($newImage, 0, 0, $transparent);
        imagecopy($newImage, $image, 0, 0, 0, 0, $width, $height);
        return $newImage;
    }
}