<?php

namespace App\Service\Stats;

use App\Entity\Hive;
use App\Entity\Visit;
use DateTimeImmutable;
use App\Tool\EditielPdf;
use App\Repository\VisitRepository;
use App\Tool\PathMaker;
use Exception;

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
}