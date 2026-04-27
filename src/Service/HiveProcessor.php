<?php

namespace App\Service;

use App\Entity\Apiculteur;
use App\Entity\Hive;
use Com\Tecnick\Barcode\Barcode;
use GdImage;

class HiveProcessor
{
    private const X_BOTTOM_LEFT = 0;
    private const X_BOTTOM_RIGHT = 2;
    private const Y_TOP_LEFT = 5;
    private const Y_BOTTOM_LEFT = 1;

    public static function generateQR(Apiculteur $user, Hive $hive): string
    {
        $userId = $user->getId();
        $apiaryId = $hive->getApiary()->getId();
        $hiveId = $hive->getIdentification();
        $qrCodeValue = "{$userId}-{$apiaryId}-{$hiveId}";
        $qrCode = md5($qrCodeValue);
        return $qrCode;
    }

    public static function generatePoster(Hive $hive, string $font, ?int $whishWidth = 0): GdImage
    {
        $texts = [
            'nom de la ruche :' => $hive->getName(),
            'numero de la ruche :' => $hive->getIdentification(),
            'Nom du rucher :' => $hive->getApiary()->getName(),
            'Numéro de rucher :' => $hive->getApiary()->getIdentification()
        ];
        $qrWidth = 410;
        if(240 <= $whishWidth) {
            $qrWidth = $whishWidth;
        }
        $qrHeight = $qrWidth;
        $marginHeight = 20;
        $marginWidth = 20;
        $fontSize = 10;

        $pictureWidth = $qrWidth + 2 * $marginWidth;
        $lineHeight = self::calculateLineHeight('nom de la ruche', $font, $fontSize);
        $count = count($texts);
        $pictureHeight = $qrHeight + ($count * $lineHeight) + ($count + 1) * $marginHeight;

        $barCode = new Barcode();
        $hiveCode = $hive->getQrCode();
        if (null !== $hiveCode) {
            $qrCode = $barCode->getBarcodeObj('QRCODE', $hive->getQrCode(), $qrWidth, $qrHeight);
            $qrPicture = $qrCode->getGd();
        } else {
            $qrPicture = self::makeNoQrCode($qrWidth, $qrHeight, $font);
        }

        $poster = self::makePoster($pictureWidth, $pictureHeight, $marginWidth, $marginHeight, $qrPicture, $texts, $fontSize, $font);

        return $poster;
    }

    private static function makePoster(int $width, int $height, int $marginX, int $marginY, GdImage $qr, array $texts, int $fontsize, string $font): GdImage
    {
        $poster = imagecreatetruecolor($width, $height);
        $lineHeight = self::calculateLineHeight('nom de la ruche', $font, $fontsize);
        $white = imagecolorallocate($poster, 255, 255, 255);
        $black = imagecolorallocate($poster, 0, 0, 0);
        imagefill($poster, 0, 0, $white);
        $x = $marginX;
        $y = $marginY;
        $qrWidth = imagesx($qr);
        $qrHeight = imagesy($qr);
        imagecopy($poster, $qr, $x, $y, 0, 0, $qrWidth, $qrHeight);
        $topText = 2 * $marginY + $qrHeight;
        foreach ($texts as $label => $value) {
            imagettftext($poster, $fontsize, 0, $marginX, $topText, $black, $font, "{$label} {$value}");
            $topText += $lineHeight + $marginY;
        }
        return $poster;
    }

    private static function makeNoQrCode(int $width, int $height, string $font): GdImage
    {
        $fontSize = 72;
        $pictureCenter = intdiv($width, 2);
        $top = 133;
        $left = 40;
        $left = self::CenterText('NO QR', $font, $fontSize, $pictureCenter);
        $margin = 40;
        $picture = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($picture, 255, 255, 255);
        $black = imagecolorallocate($picture, 0, 0, 0);
        imagefill($picture, 0, 0, $white);
        $values = imagettftext($picture, $fontSize, 0, $left, $top, $black, $font, 'NO QR');
        $lineTop = $top + $fontSize + $margin;
        $center = self::GetCenter($values[self::X_BOTTOM_LEFT], $values[self::X_BOTTOM_RIGHT]);
        $lineLeft =  $left + self::CenterText('CODE', $font, $fontSize, $center);
        imagettftext($picture, $fontSize, 0, $lineLeft, $lineTop,  $black, $font, 'CODE');
        return $picture;
    }

    private static function GetCenter(int $xMin, int $xMax): int
    {
        $length = $xMax - $xMin;
        return intdiv($length, 2);
    }

    private static function CenterText(string $text, string $font, int $fontsize, int $xMiddle): int
    {
        $coord = imagettfbbox($fontsize, 0, $font, $text);
        $width = $coord[self::X_BOTTOM_RIGHT] - $coord[self::X_BOTTOM_LEFT];
        $center = intdiv($width, 2);
        return  $xMiddle - $center;
    }

    private static function calculateLineHeight(string $text, string $font, int $fontsize): int
    {
        $coord = imagettfbbox($fontsize, 0, $font, $text);
        return $coord[self::Y_BOTTOM_LEFT] - $coord[self::Y_TOP_LEFT];
    }
}
