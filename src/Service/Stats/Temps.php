<?php

namespace App\Service\Stats;

use App\Dto\TempDto;
use App\Entity\Hive;
use DateTimeImmutable;
use App\Entity\Apiculteur;
use App\Repository\Archive\VisitsRepository;
use App\Repository\StatsDataRepository;
use App\Tool\PathMaker;
use App\Tool\SingleGraph;

class Temps
{
    public function __construct(private StatsDataRepository $repo, private VisitsRepository $archive) {}

    public function getTemps(Hive $hive, array $dates, Apiculteur $user, string $rooPath): array
    {
        $dates = $this->formatDate($dates);
        $startDate = $dates['startDate'];
        $endDate = $dates['endDate'];

        $result['path'] = '';
        $result['startDate'] = null === $startDate ? "Début" : $startDate->format('d/m/Y');;
        $result['endDate'] = null === $endDate ? "Aujourd'hui" : $endDate->format('d/m/Y');

        /**@var TempDto[] $datas */
        $datas = $this->getDatas($hive, $startDate, $endDate, $user);
        if (false === $datas) {
            $result['datas'] = [];
            return $result;
        }

        $path = $this->makePicturePath($user, $hive, $rooPath);
        $result['path'] = $path;

        $fullPath = $rooPath . $path;
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
        $this->drawGraph($datas, $hive, $result['startDate'], $result['endDate'], $fullPath);
        return $result;
    }

    private function formatDate(array $dates): array
    {
        $startDate = $dates['startDate'];
        $endDate = $dates['endDate'];
        if (null !== $startDate) {
            $startDate = DateTimeImmutable::createFromMutable($startDate);
        }

        if (null !== $endDate) {
            $endDate = DateTimeImmutable::createFromMutable($endDate);
        }
        return [
            'startDate' => $startDate,
            'endDate' => $endDate
        ];
    }

    private function getDatas(Hive $hive, ?DateTimeImmutable $startDate, ?DateTimeImmutable $endDate, Apiculteur $user): array | false
    {
        $datas = $this->repo->findTempsData($hive, $startDate, $endDate);
        $datasArchives = $this->archive->findTempForStats($hive, $user, $startDate, $endDate);
        $datas = array_merge($datas, $datasArchives);
        if (0 === count($datas)) {
            return false;
        }
        usort($datas, fn($a, $b) => $a->date <=> $b->date);
        return $datas;
    }

    private function makePicturePath(Apiculteur $user, Hive $hive, string $rootPath): string
    {
        $relativePath = PathMaker::makeStatPicturePath($user, $hive, $rootPath);
        $filename = "temperature.png";
        return $relativePath . $filename;
    }

    private function drawGraph(array $values, Hive $hive, string $start, string $end, string $path)
    {
        $title = "Relevé des températures de la ruche {$hive->getName()} sur la période du {$start} jusqu'à {$end}";
        $drawer = new SingleGraph(800, 400);
        $drawer->draw($title, $values);
        $drawer->save($path);
    }
}
