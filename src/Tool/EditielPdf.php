<?php

namespace App\Tool;

use App\Entity\Visit;
use Fpdf\Fpdf;

class EditielPdf extends Fpdf
{
    protected string $documentTitle = '';
    protected string $userName = '';
    protected string $userFirstname = '';
    protected string $adress = '';
    protected string $adressComp = '';
    protected string $napi = '';
    protected string $siret = '';
    protected string $loc = '';
    protected string $apiaryNumber = '';
    protected string $apiaryName = '';
    protected string $hiveNumber = '';

    public function addCell(int $w, int $h = 0, string $txt = '', mixed $border = 0, int $ln = 0, string $align = '', bool $fill = false, string $link = ''): void
    {
        $newTxt = $this->utf8Decode($txt);
        parent::Cell($w, $h, $newTxt, $border, $ln, $align, $fill, $link);
    }

    public function addMultiCell(float $w, float $h, string $txt,  mixed $border = 0, ?string $align = 'J', ?bool $fill = false): void
    {
        $newTxt = $this->utf8Decode($txt);
        parent::MultiCell($w, $h, $newTxt, $border, $align, $fill);
    }

    public function header(): void
    {
        $this->SetFont('Arial', 'B', 15);
        $title = $this->documentTitle;
        $titleWitdh = $this->GetStringWidth($title) + 6;
        $this->setX((210 - $titleWitdh) / 2);
        $this->SetLineWidth(1);
        $this->addCell($titleWitdh, 9, $title, 1, 1, 'C', false);
        $this->ln(10);
    }

    public function cellCenter(string $text): void
    {

        $textWidth = $this->GetStringWidth($text) + 6;
        $this->setX((210 - $textWidth) / 2);
        $this->addCell($textWidth, 0, $text, 0, 0, 'C', false);
    }

    public function setDocTitle(string $docTitle): void
    {
        $this->documentTitle = $docTitle;
    }

    public function footer(): void
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(128);
        $this->addCell(0, 10, '(c) Editiel98    Page ' . $this->PageNo(), 0, 0, 'C');
    }

    public function setInfoApi(string $name, string $firstname, string $adress, string $adressComp, string $napi, ?string $siret): void
    {
        $this->userName = $name;
        $this->userFirstname = $firstname;
        $this->adress = $adress;
        $this->adressComp = $adressComp;
        $this->napi = $napi;
        $this->siret = $siret ?? '';
    }

    public function displayApi(): void
    {
        $this->SetFont('Arial', 'B', 10);
        $width = $this->GetStringWidth('Nom : ');
        $this->addCell($width, 0, 'Nom : ', 0, 0);
        $this->SetFont('Arial', '', 10);
        $this->addCell(0, 0, $this->userName);
        $this->ln(6);

        $this->SetFont('Arial', 'B', 10);
        $width = $this->GetStringWidth('Prenom : ');
        $this->addCell($width, 0, 'Prénom : ', 0, 0);
        $this->SetFont('Arial', '', 10);
        $this->addCell(0, 0, $this->userFirstname, 0, 0, 'L');
        $this->ln(6);

        $this->SetFont('Arial', 'B', 10);
        $this->addCell(0, 0, 'Adresse : ', 0, 0);
        $this->SetFont('Arial', '', 10);
        $this->ln(6);
        $this->addCell(0, 0, $this->adress);
        $this->ln(4);
        $this->addCell(0, 0, $this->adressComp);
        $this->ln(6);

        $this->SetFont('Arial', 'B', 10);
        $width = $this->GetStringWidth('NAPI : ');
        $this->addCell($width, 0, 'NAPI : ', 0, 0);
        $this->SetFont('Arial', '', 10);
        $this->addCell(0, 0, $this->napi);
        $this->ln(6);

        $this->SetFont('Arial', 'B', 10);
        $width = $this->GetStringWidth('SIRET : ');
        $this->addCell($width, 0, 'SIRET : ', 0, 0);
        $this->SetFont('Arial', '', 10);
        $this->addCell(0, 0, $this->siret);
        $this->ln(6);
    }

    public function displayHeader(string $header): void
    {
        $this->SetFont('Arial', 'BU', 15);  //Arial, Bold, 15
        $titleWidth = $this->GetStringWidth($header) + 6; //6 pour la marge
        $this->addCell($titleWidth, 0, $header, 0, 0, 'R');
        $this->SetFont('Arial', '', 10);
    }

    function setRucher(string $apiaryName, string $apiaryNumber, string $localisation, int $hiveNumber)
    {
        $this->apiaryName = $apiaryName;
        $this->apiaryNumber = $apiaryNumber;
        $this->loc = $localisation;
        $this->hiveNumber = $hiveNumber;
    }

    public function displayApiary(): void
    {
        $this->SetFont('Arial', 'B', 10);
        $width = $this->GetStringWidth('Nom : ');
        $this->addCell($width, 0, 'Nom : ', 0, 0);
        $this->SetFont('Arial', '', 10);
        $this->addCell(0, 0, $this->apiaryName);
        $this->ln(6);

        $this->SetFont('Arial', 'B', 10);
        $width = $this->GetStringWidth('Numero : ');
        $this->addCell($width, 0, 'Numéro : ', 0, 0);
        $this->SetFont('Arial', '', 10);
        $this->addCell(0, 0, $this->apiaryNumber);
        $this->ln(6);

        $this->SetFont('Arial', 'B', 10);
        $width = $this->GetStringWidth('Localisation : ');
        $this->addCell($width, 0, 'Localisation : ', 0, 0);
        $this->SetFont('Arial', '', 10);
        $this->addCell(0, 0, $this->loc);
        $this->ln(6);

        $this->SetFont('Arial', 'B', 10);
        $width = $this->GetStringWidth('Nombre de ruches : ');
        $this->addCell($width, 0, 'Nombre de ruches : ', 0, 0);
        $this->SetFont('Arial', '', 10);
        $this->addCell(0, 0, $this->hiveNumber);
        $this->ln(6);
    }

    public function displayVisit(Visit $visit): void
    {
        $this->SetFont('Arial', '', 10);
        $dateWitdh = $this->GetStringWidth('Date de la visite : ');
        $this->addCell($dateWitdh, 0, 'Date de la visite : ', 0, 0);
        $this->addCell(0, 0, $visit->getDate()->format('d/m/Y'));
        $this->ln(10);

        $this->addCell(10);
        $this->SetFont('Arial', 'U', 10);
        $this->addCell(0, 0, 'Informations sur la ruche');
        $this->ln(7);

        $this->SetFont('Arial', 'BU', 10);
        $width = $this->GetStringWidth("Comportement de l'essaim");
        $this->addCell(15);
        $this->addCell($width, 0, "Comportement de l'essaim");
        $this->SetFont('Arial', '', 10);
        $this->addCell(0, 0, ' : ' . $visit->getBehaviour() ?? '-');
        $this->ln(7);

        $this->SetFont('Arial', 'BU', 10);
        $width = $this->GetStringWidth("Etat de la population");
        $this->addCell(15);
        $this->addCell($width, 0, "Etat de la population");
        $this->SetFont('Arial', '', 10);
        $this->addCell(0, 0, ' : ' . $visit->getPopulation() ?? '-');
        $this->ln(7);

        $this->SetFont('Arial', 'BU', 10);
        $width = $this->GetStringWidth("Maladie : ");
        $this->addCell(15);
        $this->addCell($width, 0, "Maladie :");
        $this->SetFont('Arial', '', 10);
        $disease = $visit->isDisease() ? ' Oui :' : ' Aucune';
        $width = $this->GetStringWidth(' : ' . $disease . ' ');
        $this->addCell($width, 0,  $disease . ' ');
        $diseaseName = $visit->getDisease() === null ? '' : $visit->getDisease()->getName();
        $this->addCell(0, 0, $diseaseName);
        $this->ln(7);

        $this->SetFont('Arial', 'BU', 10);
        $width = $this->GetStringWidth("Reine visible : ");
        $this->addCell(15);
        $this->addCell($width, 0, "Reine visible : ");
        $this->SetFont('Arial', '', 10);
        $this->addCell(0, 0, ' : ' . $visit->isQueenVisible() ? ' Oui' : ' Non');
        $this->ln(15);

        $this->addCell(10);
        $this->SetFont('Arial', 'U', 10);
        $this->addCell(0, 0, 'Mesures effectuées');
        $this->ln(7);

        $this->SetFont('Arial', 'BU', 10);
        $width = $this->GetStringWidth("Temperature : ");
        $this->addCell(15);
        $this->addCell($width, 0, "Température :");
        $this->SetFont('Arial', '', 10);
        $temperature = $visit->getTemperature() === null ? 'Non mesuré' : $visit->getTemperature() .'°C';
        $this->addCell(0, 0,  $temperature);
        $this->ln(7);

        $this->SetFont('Arial', 'BU', 10);
        $width = $this->GetStringWidth("Climat");
        $this->addCell(15);
        $this->addCell($width, 0, "Climat");
        $this->SetFont('Arial', '', 10);
        $this->addCell(0, 0, ' : ' . $visit->getWeather()->label());
        $this->ln(7);

        $this->SetFont('Arial', 'BU', 10);
        $width = $this->GetStringWidth("Hygrometrie");
        $this->addCell(15);
        $this->addCell($width, 0, "Hygrométrie");
        $this->SetFont('Arial', '', 10);
        $hygometry = $visit->getHygrometry() === null ? 'Non mesuré' : $visit->getHygrometry() .'%';
        $this->addCell(0, 0, ' : ' . $hygometry);
        $this->ln(7);

        $this->SetFont('Arial', 'BU', 10);
        $width = $this->GetStringWidth("Poids");
        $this->addCell(15);
        $this->addCell($width, 0, "Poids");
        $this->SetFont('Arial', '', 10);
        $weight = $visit->getWeight() === null ? 'Non mesuré' : $visit->getWeight() .' kg';
        $this->addCell(0, 0, ' : ' . $weight);
        $this->ln(15);

        $this->addCell(10);
        $this->SetFont('Arial', 'U', 10);
        $this->addCell(0, 0, 'Actions effectuées');
        $this->ln(7);

        $this->SetFont('Arial', 'BU', 10);
        $width = $this->GetStringWidth("Nourrissage : ");
        $this->addCell(15);
        $this->addCell($width, 0, "Nourrissage :");
        $this->SetFont('Arial', '', 10);
        $feeding = $visit->isFeeded() ? 'Oui :' : 'non effectué';
        $width = $this->GetStringWidth($feeding . '  ');
        $this->addCell($width, 0, $feeding);
        $this->addCell(0, 0, ' ' . $visit->getFeeding());
        $this->ln(7);

        $this->SetFont('Arial', 'BU', 10);
        $width = $this->GetStringWidth("Travaux a prevoir : ");
        $this->addCell(15);
        $this->addCell($width, 0, "Travaux à prévoir :");
        $this->SetFont('Arial', '', 10);
        $this->addCell(0, 0, $visit->isWorksToDo() ? 'Oui': 'Non');
        $this->ln(15);

        $this->addCell(10);
        $this->SetFont('Arial', 'U', 10);
        $this->addCell(0, 0, 'Notes');
        $this->ln(7);
        $this->SetFont('Arial', '', 10);
        $this->addCell(15);
        $this->addMultiCell(0, 4, $visit->getNotes() ?? 'Aucunes');
    }

    private function utf8Decode(string $toConvert): string
    {
        return mb_convert_encoding($toConvert, 'ISO-8859-1', 'UTF-8');
    }
}
