<?php

namespace App\Service\Stats;

use App\Entity\Hive;
use DateTimeImmutable;
use App\Dto\HarvestDto;
use App\Tool\LineGraph;
use App\Tool\PathMaker;
use App\Entity\Apiculteur;
use App\Dto\HarvestHiveDto;
use App\Repository\HarvestRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class Harvest
{
    public function __construct(
        private HarvestRepository $repo,
        #[Autowire('%kernel.project_dir%/public/uploads/')] private string $uploadDirectory
    ) {}

    public function getHarvest(array $dates, Apiculteur $user, Hive $hive, string $rootPath): HarvestHiveDto
    {

        $dto = new HarvestHiveDto();
        $dates = $this->formatDate($dates);
        $startDate = $dates['startDate'];
        $endDate = $dates['endDate'];
        $dto->startDate = null === $startDate ? "Début" : $startDate->format('d/m/Y');;
        $dto->endDate = null === $endDate ? "Aujourd'hui" : $endDate->format('d/m/Y');
        $harvests = $this->repo->findByHiveBeekeeperDate($user, $hive);
        if (0 >= count($harvests)) {
            return $dto;
        }
        $dto->isFilled = true;
        $dto->totalWeight = $this->getTotalWeight($harvests);
        $dto->harvests = $this->calcWeigthByType($harvests);
        $path = PathMaker::makeHarvestPicturePath($user, $hive, $rootPath);
        $fullPath = $rootPath . $path;
        $file = $this->drawGraph($harvests, $fullPath, $hive->getName(), $dto->startDate, $dto->endDate, $rootPath);
        $dto->file = $path . $file;
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
     * get total weight for the hive
     *
     * @param HarvestDto[] $harvests
     * @return float
     */
    private function getTotalWeight(array $harvests): float
    {
        $total = 0;
        foreach ($harvests as $harvest) {
            $total += $harvest->weight;
        }
        return $total;
    }

    /**
     * get total weight for the hive by types
     *
     * @param HarvestDto[] $harvests
     * @return array
     */
    private function calcWeigthByType(array $harvests): array
    {
        $returnArray = [];
        foreach ($harvests as $harvest) {
            $type = $harvest->type;
            if (!key_exists($type, $returnArray)) {
                $returnArray[$type] = $harvest->weight;
            } else {
                $returnArray[$type] += $harvest->weight;
            }
        }
        return $returnArray;
    }

    /**
     * Create graph for all harvests
     *
     * @param HarvestDto[] $harvests
     * @param string $path
     * @param string $hiveName
     * @param string $from
     * @param string $to
     * @return string|null
     */
    private function drawGraph(array $harvests, string $path, string $hiveName, string $from, string $to, string $rootPath): ?string
    {
        $filename = 'harvest.png';
        $title = "Relevé des récoltes de la ruche '{$hiveName}' sur la période du {$from} jusqu'à {$to}";
        $fullPath = $path . $filename;

        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
        $harvests = $this->sortByDate($harvests);
        $minDate = $harvests[0]->date->getTimestamp();
        $maxDate = end($harvests)->date->getTimestamp();
        $splits = $this->splitArray($harvests);
        $graph = new LineGraph(800, 500);
        $graph->setMinMax($minDate, $maxDate);
        foreach ($splits as $type => $datas) {
            $this->drawLine($datas, $type, $graph, $rootPath);
        }
        $graph->drawGraph($title, 2, 60);
        $graph->save($fullPath);
        return $filename;
    }

    /**
     * 
     * @param HarvestDto[] $datas
     * @return HarvestDto[]
     */
    private function sortByDate(array $datas): array
    {
        usort($datas, fn($a, $b) => $a->date <=> $b->date);
        return $datas;
    }

    /**
     * Split Harvests array into sub arrays by types
     *
     * @param HarvestDto[] $harvests
     * @return array<string, HarvestDto[]>
     */
    private function splitArray(array $harvests): array
    {
        $returnArray = [];
        foreach ($harvests as $harvest) {
            $key = $harvest->type;
            $returnArray[$key][] = $harvest;
        }
        return $returnArray;
    }

    /**
     * Undocumented function
     *
     * @param HarvestDto[] $values
     * @param string $type
     * @param LineGraph $draw
     * @return void
     */
    private function drawLine(array $values, string $type, LineGraph $draw, string $rootPath): void
    {
        $dateArray = [];
        $valueArray = [];
        foreach ($values as $value) {
            $dateArray[] = $value->date->getTimestamp();
            $valueArray[] = $value->weight;
        }
        $picture = $values[0]->picture;
        if (null === $picture) {
            $pictureRoot = PathMaker::makeHoneyFolder($rootPath);
            $picture  = $pictureRoot . 'default.png';
        }
        $picturePath = $this->uploadDirectory . $picture;
        $draw->addMultiValuesLine($valueArray, $dateArray, $type, 'teal', true, $picturePath, 'blue');
    }
}
