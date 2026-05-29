<?php

namespace App\Tool;

use App\Entity\Apiary;

class ApiaryPdf extends PdfBase
{
    public function __construct(private string $title){
        parent::__construct();
    }

    public function SetDocTitle(string $title)
    {
        $this->$title = $title;
    }

    public function header()
    {
        $this->SetFont('Arial', 'B', 15);
        $titleWidth = $this->GetStringWidth($this->title) + 6;
        $this->setX((210 - $titleWidth) / 2);
        $this->SetLineWidth(1);
        $this->addCell($titleWidth, 9, $this->title, 1, 1, 'C', false);
        $this->ln(10);
    }

    public function footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(128);
        $this->addCell(0, 10, '(c) Editiel98    Page ' . $this->PageNo(), 0, 0, 'C');
    }

    public function displayApiaryInfo(Apiary $apiary)
    {
        $user = $apiary->getOwner();
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(0);
        $line = "Apiculteur : {$user->getName()} {$user->getFirstname()}";
        $this->addCell(0, 0, $line, 0, 0);
        $this->ln(15);
        $this->addCell(5, 0);
        $this->SetFont('Arial', 'BU', 15);
        $this->addCell(0, 0, 'Informations sur le rucher', 0, 0);
        $this->ln(10);
        $this->SetFont('Arial', 'U', 10);
        $titleWidth = $this->GetStringWidth('Nom du rucher');
        $this->addCell($titleWidth, 0, 'Nom du rucher', 0, 0);
        $this->SetFont('Arial', '', 10);
        $this->addCell(0, 0, " :   {$apiary->getName()}", 0, 0);
        $this->ln(7);
        $width = $this->GetStringWidth('Numero du rucher');
        $this->SetFont('Arial', 'U', 10);
        $this->addCell($width, 0, 'Numéro du rucher', 0, 0);
        $this->SetFont('Arial', '', 10);
        $this->addCell(0, 0, " : {$apiary->getIdentification()}", 0, 0);
        $this->ln(7);
        $width = $this->GetStringWidth('Localisation');
        $this->SetFont('Arial', 'U', 10);
        $this->addCell($width, 0, 'Localisation', 0, 0);
        $this->SetFont('Arial', '', 10);
        $this->addCell(0, 0, " : {$apiary->getLocalisation()}", 0, 0);
        $status = $apiary->isActive() ? 'Actif' : 'Mort';
        $this->ln(7);
        $width = $this->GetStringWidth('Etat');
        $this->SetFont('Arial', 'U', 10);
        $this->addCell($width, 0, 'Etat', 0, 0);
        $this->SetFont('Arial', '', 10);
        $this->addCell(0, 0, " : {$status}", 0, 0);
        $this->ln(7);
        $this->SetFont('Arial', 'U', 10);
        $width = $this->GetStringWidth('Observations');
        $this->AddCell($width, 0, 'Observations', 0, 0);
        $this->SetFont('Arial', '', 10);
        $this->addCell(0, 0, " : ", 0, 0);
        $this->ln(7);
        $this->SetX(20);
        $this->MultiCell(0, 5, $apiary->getObservations());
        $this->ln(10);
    }

    public function setHiveNumber(int $nbHives)
    {
        $this->SetFont('Arial', 'BU', 15);
        $this->AddCell(5, 0);
        $this->AddCell(0, 0, 'Informations sur les ruches');
        $this->ln(10);
        $this->SetFont('Arial', 'U', 10);
        $width = $this->GetStringWidth('Nombre de ruches');
        $this->AddCell($width, 0, 'Nombre de ruches');
        $this->SetFont('Arial', '', 10);
        $this->AddCell(0, 0, " : {$nbHives} ruches");
        $this->ln(10);
        $this->AddCell(0, 0, 'Dont : ');
        $this->ln(7);
    }

    function setHiveState(int $number, string $state)
    {
        $this->SetX(15);
        $this->SetFont('Arial', '', 10);
        $this->addCell(0, 0, "{$number} ruches(s) {$state}");
        $this->ln(7);
    }

    function setTotalHarvest(float $weight)
    {
        //On re saute une 1 ligne
        $this->ln(7);
        $this->SetFont('Arial', 'BU', 15);
        $this->addCell(5, 0);
        $this->addCell(0, 0, 'Informations sur les récoltes');
        $this->ln(10);
        $this->SetFont('Arial', '', 10);
        $this->addCell(0, 0, "Récolte totale : {$weight} kg");
        $this->ln(10);
        $this->SetFont('Arial', 'U', 10);
        $width = $this->GetStringWidth('Recolte par type de miel');
        $this->addCell($width, 0, 'Récolte par type de miel');
        $this->SetFont('Arial', '', 10);
        $this->addCell(0, 0, ' : ');
        $this->ln(7);
    }

    function setHarvestType(string $type, float $weight)
    {
        $this->SetX(15);
        $this->SetFont('Arial', '', 10);
        $this->addCell(0, 0, "{$type} : {$weight} kg");
        $this->ln(7);
    }

    function setPicture(string $picture, string $title)
    {
        $this->ln(15);
        $this->SetFont('Arial', 'B', 15);
        $this->addCell(0, 0, $title);
        $this->ln(20);
        $xGraph = 2;
        $this->image($picture, $xGraph);
    }
}
