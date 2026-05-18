<?php

namespace App\Tool;

use App\Entity\Hive;

class Results extends PdfBase
{
    private string $title;
    private float $bottomRect;

    public function __construct(string $orientation, ?string  $unit = 'mm', ?string $size = 'A4')
    {
        return parent::__construct($orientation, $unit, $size);
    }

    function setDocTitle(string $title): void
    {
        $this->title = $title;
    }

    public function footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(128);
        $today = date('d/m/Y');
        $this->AddCell(0, 10, "Document produit le {$today}");
        $this->ln(7);
        $this->addCell(0, 10, '(c) Editiel98    Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    public function Header()
    {
        $this->SetFont('Arial', 'B', 15);
        $this->title;
        $titleWidth = $this->GetStringWidth($this->title) + 6;
        $this->setX((297 - $titleWidth) / 2);
        $this->SetLineWidth(1);
        $this->addCell($titleWidth, 9, $this->title, 1, 1, 'C', false);
        $this->ln(10);
    }

    public function subtitle(string $from, string $to): void
    {
        $this->SetFont('Arial', 'B', 10);
        $subtitleWidth = $this->GetStringWidth("Releve de la ruche entre le {$from} et le {$to}");
        $this->setX((297 - $subtitleWidth) / 2);
        $this->addCell(0, 0, "Relevé de la ruche entre le {$from} et le {$to}");
        $this->ln(10);
    }

    public function setHiveInfos(Hive $hive): void
    {
        $oldX = $this->GetX() + 30;
        $oldY = $this->GetY();
        $width = 180;
        $height = 90;
        $margin = 5;
        $this->bottomRect = $oldY + $height + $margin;
        $this->Rect($oldX, $oldY, $width, $height);
        $this->SetY($oldY + $margin);
        $infoWidth = $this->GetStringWidth("Informations sur la ruche");
        $xCell = (($oldX + $width) - $infoWidth) / 2;
        $this->SetX($xCell);
        $this->SetFont('Arial', 'BU', 10);
        $this->Cell(0, 0, "Informations sur la ruche");
        $this->SetFont('Arial', '', 10);
        $this->ln(7);
        $this->setX($oldX + $margin);
        $this->addInfoLine('Rucher : ', 'Rucher :', $hive->getApiary()->getName());
        //End of Line
        $this->setX($oldX + $margin);
        $this->addInfoLineWithMargin('Nom de la ruche', 'Nom de la ruche', $hive->getName());
        //Second Line
        $this->initLine();
        $this->addInfoLine('Numero de la ruche', 'Numéro de la ruche', " : {$hive->getIdentification()}");
        //End of second line
        $this->setX($oldX + $margin);
        $this->SetFont('Arial', 'U', 10);
        $this->addInfoLineWithMargin("Date de debut d'exploitation", "Date de début d'exploitation", $hive->getDate()->format('d/m/Y'));
        //Second info of the line
        $this->initLine();
        $this->addInfoLine('Type de ruche', 'Type de ruche', " : {$hive->getKind()->getName()}");
        //End onf third Line
        $this->setX($oldX + $margin);
        $this->SetFont('Arial', 'U', 10);
        $this->addInfoLineWithMargin("Nombre de cadres", "Nombre de cadres", $hive->getFrameNumber());
        //Second info of the line
        $this->initLine();
        $this->addInfoLine('Nombre de hausses', 'Nombre de hausses', " : {$hive->getRiseNumber()}");
        //End of 4th line
        $this->setX($oldX + $margin);
        $this->addInfoLine('Etat', 'Etat', " : {$hive->getState()->translate()}");
        //Observations
        $this->setX($oldX + $margin);
        $this->SetFont('Arial', 'U', 10);
        $this->addCell(0, 0, "Observations");
        $this->SetFont('Arial', '', 8);
        $this->ln(5);
        $var = $hive->getObservation();
        $this->setX($oldX + $margin + 1);
        $cellWidth = $width - $margin - 1;
        $this->MultiCell($cellWidth, 3, $var);
        $this->ln(10);
    }

    public function setGraph(string $path, string $title, ?int $width = 0, ?int $height = 0)
    {
        $this->ln(15);
        $this->SetFont('Arial', 'B', 15);
        $this->addCell(0, 0, $title);
        $this->ln(20);
        $width = 200;
        $xGraph = (297 - $width) / 2;
        $this->image($path, $xGraph, null, $width, $height);
    }

    public function setHarvests(string $total, array $byTypes): void
    {
        $this->SetY($this->bottomRect);
        $this->SetFont('Arial', 'U', 15);
        $this->addCell(0, 0, "Informations sur les récoltes");
        $this->ln(7);
        $this->SetFont('Arial', 'U', 10);
        $width = $this->GetStringWidth("Poids total recolte");
        $this->SetX(15);
        $this->addCell($width, 0, "Poids total recolté");
        $this->SetFont('Arial', '', 10);
        $this->addCell(0, 0, " : {$total} kg");
        $this->SetFont('Arial', 'U', 15);
        $this->ln(10);
        $this->addCell(0, 0, "Informations sur les récoltes par type de miel");
        $this->ln(7);
        $i = 0;
        foreach ($byTypes as $type => $value) {
            if (($i % 6) == 0) {
                $this->ln(7);
            }
            $this->SetFont('Arial', 'BU', 10);
            $weight = " : {$value} kg   ";
            $widthType = $this->GetStringWidth($type);
            $this->addCell($widthType, 0, $type);
            $widthType = $this->GetStringWidth($weight);
            $this->SetFont('Arial', '', 10);
            $this->addCell($widthType, 0, $weight);
            $i++;
        }
    }

    public function dataloggerInfos(string $from, string $to, array $dataloggers): void
    {
        $text = "Le datalogger est un système électronique programmable d'enregistrement de données concernant la ruche. Ce système permet un suivi de l'évolution de la ruche
		en mesurant le poids, les températures et l'hygrométrie de la ruche et de son environnement.
		Ils sont identifiés par un numéro unique et associés à la ruche via un fichier de configuration créé par le logiciel.";
        $this->SetFont('Arial', 'BU', 15);
        $width = $this->GetStringWidth('Informations du datalogger');
        $this->SetX((297 - $width) / 2);
        $this->addCell(0, 0, 'Informations du datalogger');
        $this->ln(15);
        $this->SetFont('Arial', '', 15);
        $this->addMultiCell(0, 7, $text);
        $this->ln(10);
        if ($from == '') {
            $this->addCell(0, 0, "Il n'y a pas d'enregistrement pour cette ruche");
            $this->ln(7);
        } else {
            $this->addmultiCell(0, 7, "Les enregistreurs utilisés pour les relevés de cette ruches sur la période du {$from} au {$to} ont les numéros de série suivants :");
            $this->ln(10);
            foreach ($dataloggers as $value) {
                $this->addCell(0, 0, "-Datalogger N° : {$value}");
                $this->ln(7);
            }
        }
    }

    public function setAverage(string $weight, string $extTemp, string $intTemp, string $extHygro, string $intHygro): void
    {
        $this->SetFont('Arial', 'BU', 15);
        $this->ln(30);
        $this->addCell(0, 0, "Valeurs moyennes des relevés du datalogger");
        $this->ln(25);
        $this->addInfo($weight, 'Poids moyen', 'Poids moyen', 'Kg');
        $this->addInfo($intHygro, 'Hygrometrie interieure', 'Hygrométrie intérieure', '%');
        $this->addInfo($extHygro, 'Hygrometrie exterieure', 'Hygrométrie extérieure', '%');
        $this->addInfo($extTemp, 'Temperature interieure', 'Température extérieure', '°C');
        $this->addInfo($intTemp, 'Temperature exterieure', 'Température extérieure', '°C');
    }

    private function initLine(?int $margin = 130): void
    {
        $this->setX($margin);
        $this->SetFont('Arial', 'U', 10);
    }

    private function addInfoLine(string $template, string $title, string $value): void
    {
        $width = $this->GetStringWidth($template);
        $this->SetFont('Arial', 'U', 10);
        $this->addCell($width, 0, $title);
        $this->SetFont('Arial', '', 10);
        $this->addCell(0, 0, $value);
        $this->ln(7);
    }

    private function addInfoLineWithMargin(string $template, string $title, string $value): void
    {
        $width = $this->GetStringWidth($template);
        $this->SetFont('Arial', 'U', 10);
        $this->addCell($width, 0, $title);
        $this->SetFont('Arial', '', 10);
        $var = ' : ' . $value;
        $width = $this->GetStringWidth($var);
        $this->addCell($width, 0, $var);
    }

    private function addInfo(string $value, string $textForSize, string $text, string $unit)
    {
        $this->SetFont('Arial', 'U', 10);
        $width = $this->GetStringWidth($textForSize);
        $this->addCell($width, 0, $text);
        $this->SetFont('Arial', '', 10);
        if ($value == '')
            $this->addCell(0, 0, ' : aucun relevé');
        else
            $this->addCell(0, 0, " : {$value} {$unit}");
        $this->ln(15);
    }
}
