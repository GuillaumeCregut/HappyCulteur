<?php

namespace App\Service;

use App\Entity\Apiary;
use App\Tool\EditielPdf;
use App\Entity\Apiculteur;

class ApiaryDeclaration
{

    public function __construct(private EditielPdf $pdf) {}

    public function createdoc(Apiculteur $user, Apiary $apiary)
    {
        $this->pdf->setAuthor('Editiel98');
        $this->pdf->setCreator('Gestion Rucher');
        $this->pdf->SetTitle(html_entity_decode('Assistance déclaration du rucher'));
        $this->pdf->setDocTitle('Déclaration de rucher');
        $this->pdf->addPage();
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->SetTextColor(255, 0, 0);
        $this->pdf->addMultiCell(0, 5, "Attention ce document n'est pas un document officiel. Pour effectuer votre déclaration il faut utiliser le CERFA 13995 trouvable à l'adresse ci dessous :");
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->ln(5);
        $this->pdf->addCell(0, 0, "https://agriculture-portail.6tzen.fr/default/requests/cerfa13995/", 0, 1, 'L', false, 'https://agriculture-portail.6tzen.fr/default/requests/cerfa13995/');
        $adress = $user->getStreetNumber() . ' ' . $user->getStreet();
        $adressComp = $user->getZipCode() . ' ' . $user->getCity();
        $this->pdf->setInfoApi(
            $user->getName(),
            $user->getFirstname(),
            $adress,
            $adressComp,
            $user->getCodeAPI(),
            $user->getSiret()
        );
        $this->pdf->ln(25);
        $this->pdf->displayHeader("Informations sur l'apiculteur");
        $this->pdf->ln(10);
        $this->pdf->displayApi();
        //TODO : Change hive number
        $hiveNumber = 0;
        $this->pdf->SetRucher($apiary->getName(), $apiary->getIdentification(), $apiary->getLocalisation(), $hiveNumber);
        $this->pdf->ln(15);
        $this->pdf->displayHeader("Informations sur le rucher");
        $this->pdf->ln(10);
        $this->pdf->displayApiary();
    }

    public function save(string $path, string $filename): void
    {
        $fullPath = $path . $filename;
        $this->pdf->Output('F', $fullPath);
    }
}
