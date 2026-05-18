<?php

namespace App\Tool;

use Fpdf\Fpdf;

class PdfBase  extends Fpdf
{
    public function addCell(float $w, float $h = 0, string $txt = '', mixed $border = 0, int $ln = 0, string $align = '', bool $fill = false, string $link = ''): void
    {
        $newTxt = $this->utf8Decode($txt);
        parent::Cell($w, $h, $newTxt, $border, $ln, $align, $fill, $link);
    }

    public function addMultiCell(float $w, float $h, string $txt,  mixed $border = 0, ?string $align = 'J', ?bool $fill = false): void
    {
        $newTxt = $this->utf8Decode($txt);
        parent::MultiCell($w, $h, $newTxt, $border, $align, $fill);
    }

    protected function utf8Decode(string $toConvert): string
    {
        return mb_convert_encoding($toConvert, 'ISO-8859-1', 'UTF-8');
    }
}
