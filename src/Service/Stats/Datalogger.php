<?php

namespace App\Service\Stats;

use App\Entity\Hive;
use DateTimeImmutable;
use App\Entity\Apiculteur;
use App\Entity\Datalogger as DataloggerEntity;
use App\Dto\DataloggerStatsDto;
use App\Repository\DataloggerRepository;
use App\Tool\LineGraph;
use App\Tool\PathMaker;

class Datalogger
{
    /**@var DateTimeImmutable[] $dates */
    private array $dates = [];
    private array $extTempArray = [];
    private array $intTempArray = [];
    private array $extHygroArray = [];
    private array $intHygroArray = [];
    private array $weightArray = [];

    public function __construct(private DataloggerRepository $repo) {}

    public function getLogs(Hive $hive, array $dates, Apiculteur $user, string $rooPath): DataloggerStatsDto
    {
        $dto = new DataloggerStatsDto();
        $dto->hive = $hive;
        $dates = $this->formatDate($dates);
        $startDate = $dates['startDate'];
        $endDate = $dates['endDate'];
        $dto->startDate = null === $startDate ? "Début" : $startDate->format('d/m/Y');;
        $dto->endDate = null === $endDate ? "Aujourd'hui" : $endDate->format('d/m/Y');

        $logs = $this->repo->findByHiveBetweenDates($hive, $startDate, $endDate);
        if (0 >= count($logs)) {
            return $dto;
        }
        $this->average($logs, $dto);

        $relativePath = PathMaker::makeDataloggerPicturePath($user, $hive, $rooPath);
        $fullPath = $rooPath . $relativePath;

        $hygroPicture = $this->drawHygroGraph($hive, $fullPath, $dto->startDate, $dto->endDate);
        if (null !== $hygroPicture) {
            $dto->graphHygro = $relativePath . $hygroPicture;
        }

        $tempPicture = $this->drawTempGraph($hive, $fullPath, $dto->startDate, $dto->endDate);
        if (null !== $tempPicture) {
            $dto->graphTemp = $relativePath . $tempPicture;
        }

        $weightPicture = $this->drawWeightGraph($hive, $fullPath, $dto->startDate, $dto->endDate);
        if (null !== $weightPicture) {
            $dto->graphWeight = $relativePath . $weightPicture;
        }
        $dto->isDatasFill = true;
        return $dto;
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

    /**
     * Undocumented function
     *
     * @param DataloggerEntity[] $logs
     * @return void
     */
    private function average(array $logs, DataloggerStatsDto $dto): void
    {
        $averageExtHygro = 0;
        $averageIntHygro = 0;
        $averageWeight = 0;
        $averageExtTemp = 0;
        $averageIntTemp = 0;
        $quantityExtHygro = 0;
        $quantityIntHygro = 0;
        $quantityExtTemp = 0;
        $quantityIntTemp = 0;
        $quantityWeight = 0;

        foreach ($logs as $log) {
            $this->dates[] = $log->getDateTime();
            if (null !== $log->getWeight()) {
                $averageWeight += $log->getWeight();
                $quantityWeight++;
                $this->weightArray[] = $log->getWeight();
            }
            if (null !== $log->getExtHyrgo()) {
                $averageExtHygro += $log->getExtHyrgo();
                $quantityExtHygro++;
                $this->extHygroArray[] = $log->getExtHyrgo();
            }
            if (null !== $log->getIntHygro()) {
                $averageIntHygro += $log->getIntHygro();
                $quantityIntHygro++;
                $this->intHygroArray[] = $log->getIntHygro();
            }
            if (null !== $log->getExtTemp()) {
                $averageExtTemp += $log->getExtTemp();
                $quantityExtTemp++;
                $this->extTempArray[] = $log->getExtTemp();
            }
            if (null !== $log->getIntTemp()) {
                $averageIntTemp += $log->getIntTemp();
                $quantityIntTemp++;
                $this->intTempArray[] = $log->getIntTemp();
            }
        }
        if (0 < $quantityExtHygro) {
            $dto->averageHygroExt = round($averageExtHygro / $quantityExtHygro, 2);
        }
        if (0 < $quantityIntHygro) {
            $dto->averageHygroInt = round($averageIntHygro / $quantityIntHygro, 2);
        }
        if (0 < $quantityExtTemp) {
            $dto->averageTempExt = round($averageExtTemp / $quantityExtTemp, 2);
        }
        if (0 < $quantityIntTemp) {
            $dto->averageTempInt = round($averageIntTemp / $quantityIntTemp, 2);
        }
        if (0 < $quantityWeight) {
            $dto->averageWeight = round($averageWeight / $quantityWeight, 2);
        }
    }

    private function drawHygroGraph(Hive $hive, string $path, string $from, string $to): ?string
    {
        if (empty($this->extHygroArray) || empty($this->intHygroArray)) {
            return null;
        }
        $filename = 'hygrometry.png';
        $fullPath = $path . $filename;
        $title = "Relevé hygrométrique de la ruche {$hive->getName()} depuis {$from} jusqu'à {$to}";
        $this->drawGraphs($this->dates, $title, $fullPath, $this->extHygroArray, $this->intHygroArray,'Hygrométrie extérieure', 'teal', 'Hygrométrie intérieure', 'black');
        return $filename;
    }

    private function drawTempGraph(Hive $hive, string $path, string $from, string $to): ?string
    {
        if (empty($this->extTempArray) || empty($this->intTempArray)) {
            return null;
        }
        $filename = 'temperature.png';
        $fullPath = $path . $filename;
        $title = "Relevé de température de la ruche {$hive->getName()} depuis {$from} jusqu'à {$to}";
        $this->drawGraphs($this->dates, $title, $fullPath, $this->extTempArray, $this->intTempArray,'Température extérieure', 'teal', 'température intérieure', 'black');
        return $filename;
    }

    private function drawWeightGraph(Hive $hive, string $path, string $from, string $to): ?string
    {
        if (empty($this->weightArray) || empty($this->weightArray)) {
            return null;
        }
        $filename = 'weight.png';
        $fullPath = $path . $filename;
        $title = "Relevé de poids de la ruche {$hive->getName()} depuis {$from} jusqu'à {$to}";
        $this->drawGraphs($this->dates, $title, $fullPath, $this->intHygroArray);
        return $filename;
    }

    private function drawGraphs(
        array $xDates,
        string $title,
        string $path,
        array $line1,
        ?array $line2 = null,
        ?string $legend1 = null,
        ?string $color1 = null,
        ?string $legend2 = null,
        ?string $color2 = null
    ) {
        if(file_exists($path)) {
            unlink($path);
        }
        $graph = new LineGraph(800, 400);
        $minValue = $this->dates[0]->getTimestamp();
        $maxValue = end($this->dates)->getTimestamp();
        $xValues = [];
        foreach ($xDates as $date) {
            $xValues[] = $date->getTimeStamp();
        }
        $graph->setXAxisValues($xValues, $minValue, $maxValue);
        $graph->addLine($line1, $legend1, $color1);
        if(null !== $line2){
            $graph->addLine($line2, $legend2, $color2);
        }
        $graph->drawGraph($title);
        $graph->save($path);
    }
}
