<?php

namespace App\Tool\Graph;

use App\Dto\HarvestDto;
use App\Tool\LineGraph;
use App\Tool\PathMaker;

class HarvestGraph
{
    public function __construct(
        private string $uploadFolder,
    ) {}

    public function drawGraph(array $harvests, string $path, string $rootPath, string $hiveName): ?string
    {
        $filename = 'harvest.png';
        $fullPath = $path . $filename;
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
        usort($harvests, fn($a, $b) => $a->date <=> $b->date);
        $minDate = $harvests[0]->date->getTimestamp();
        $maxDate = end($harvests)->date->getTimestamp();
        $from = $harvests[0]->date->format('d/m/Y');
        $to = end($harvests)->date->format('d/m/Y');
        $title = "Relevé des récoltes de la ruche '{$hiveName}' sur la période du {$from} jusqu'à {$to}";
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
        $picturePath = $this->uploadFolder . $picture;
        $draw->addMultiValuesLine($valueArray, $dateArray, $type, 'teal', true, $picturePath, 'blue');
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
}
