<?php

namespace App\Service\Stats;

use App\Entity\Hive;
use App\Repository\VisitRepository;
use DateTimeImmutable;

class Visits
{
    public function __construct(private VisitRepository $repo){}

    public function getHiveVisits(Hive $hive, array $dates): array
    {
        $result =[];
        /**@var DateTimeImmutable $startDate, $endDate */
        $startDate = $dates['startDate'];
        $endDate = $dates['endDate'];
        if(null === $endDate) {
            $endDate = new DateTimeImmutable('now');
        }
        $result['startDate'] = $startDate === null ? 'début' : $startDate->format('d/m/Y');
        $result['endDate'] = $endDate->format('d/m/Y');
        $result['visits'] =  $this->repo->findByhiveAndDates($hive, $endDate, $startDate);
        return $result;
    }
}