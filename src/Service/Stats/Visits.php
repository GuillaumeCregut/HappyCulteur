<?php

namespace App\Service\Stats;

use App\Entity\Hive;
use App\Entity\Visit;
use DateTimeImmutable;
use App\Tool\EditielPdf;
use App\Repository\VisitRepository;
use App\Tool\PathMaker;
use Exception;
use RuntimeException;

class Visits
{
    public function __construct(private VisitRepository $repo, private EditielPdf $pdf) {}

    public function getHiveVisits(Hive $hive, array $dates, string $basePath): array
    {
        $result = [];
        /**@var \DateTime $startDate, $endDate */
        $startDate = $dates['startDate'];
        $endDate = $dates['endDate'];
        if (null === $endDate) {
            $endDate = new DateTimeImmutable('now');
        } else {
            $endDate = DateTimeImmutable::createFromMutable($endDate);
        }
        if(null !== $startDate) {   
            $startDate = DateTimeImmutable::createFromMutable($startDate);
        }
        $result['startDate'] = $startDate === null ? 'début' : $startDate->format('d/m/Y');
        $result['endDate'] = $endDate->format('d/m/Y');
        $visits =  $this->repo->findByhiveAndDates($hive, $endDate, $startDate);
        $result['visits'] =  $this->repo->findByhiveAndDates($hive, $endDate, $startDate);
        if (0 < count($visits)) {
            if (null === $startDate) {
                $startDate = new DateTimeImmutable('1970-01-01');
            }
            $result['path'] = $this->buildPdf($visits, $hive, $startDate, $endDate, $basePath);
        } else {
            $result['path'] = null;
        }
        return $result;
    }

    private function buildPdf(array $visits, Hive $hive, DateTimeImmutable $start, DateTimeImmutable $end, string $basePath): string
    {
        $path = PathMaker::makeUserHiveStatPath($hive->getOwner(), $hive, 'visits', $basePath);
        $filename = $hive->getIdentification() . '.pdf';
        $filePath = $path . $filename;
        $this->pdf->setAuthor('Editiel98');
        $this->pdf->setCreator('Gestion Rucher');
        $this->pdf->SetTitle(html_entity_decode("Historique des visites de la ruche {$hive->getName()}"));
        $this->pdf->setDocTitle("Historique des visites de la ruche {$hive->getName()}");
        $this->pdf->addPage();
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->cellCenter("Du {$start->format('d/m/Y')} au {$end->format('d/m/Y')}");
        $this->pdf->ln(10);  
        $i = 0;
        $Max = count($visits);
        /**@var Visit $visit */
        foreach ($visits as $visit) {
            $this->pdf->displayVisit($visit);
            $i++;
            if ($i < $Max) {
                $this->pdf->addPage();
            }
        }
        try {

            $this->pdf->Output('F', $filePath); 
        } catch (Exception $e) {
            throw new RuntimeException('Error generating visit pdf file : ' . $e->getMessage());
        }
        return str_replace($basePath, '', $filePath);
    }
}
