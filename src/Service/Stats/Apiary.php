<?php

namespace App\Service\Stats;

use App\Tool\ApiaryPdf;
use App\Constant\HiveState;
use App\Dto\HarvestDto;
use App\Entity\Apiary as EntityApiary;
use App\Repository\HarvestRepository;
use App\Tool\PathMaker;

class Apiary
{
    public function __construct(private HarvestRepository $repo) {}

    public function getApiaryStats(EntityApiary $apiary, string $rootPath): array
    {
        $relativePath = PathMaker::makeApiaryStatsPath($apiary, $rootPath);
        $returnArray = [];
        $pdf = new ApiaryPdf('Statistique du rucher');
        $this->initPdf($pdf);
        $pdf->displayApiaryInfo($apiary);
        $pdf->setHiveNumber(count($apiary->getHives()));
        $hivesStates = $this->getHiveStats();
        foreach ($apiary->getHives() as $hive) {
            $state = $hive->getState()->translate();
            $hivesStates[$state]++;
        }
        $returnArray['hivesStates'] = $hivesStates;
        foreach ($hivesStates as $name => $quantity) {
            if (0 !== $quantity) {
                $pdf->setHiveState($quantity, $name);
            }
        }

        $harvests = $this->repo->findHarvestsByApiary($apiary, $apiary->getBeekeeper());;
        $totalHarvest = $this->getTotalHarvest($harvests);
        $returnArray['totalHarvest'] = $totalHarvest;
        $pdf->setTotalHarvest($totalHarvest);
        $harvestsType = $this->sortHarvests($harvests);
        $returnArray['harvests'] = $harvestsType;
        foreach ($harvestsType as $name => $weight) {
            $pdf->setHarvestType($name, $weight);
        }
        if (null !== $apiary->getLastPicture() && '' !== $apiary->getLastPicture()) {
            $title = "Cartographie du rucher";
            $filename = $apiary->getLastPicture();
            $fullPath = $rootPath . $filename;
            $pdf->setPicture($fullPath, $title);
        }
        $filename = $relativePath . 'results.pdf';
        $pdf->Output('F', $rootPath . $filename);
        $returnArray['path'] = $filename;
        return $returnArray;
    }

    private function initPdf(ApiaryPdf $pdf): void
    {
        $pdf->setAuthor('Editiel98');
        $pdf->setCreator('Gestion Rucher');
        $pdf->SetTitle(html_entity_decode('Statistique du rucher'));
        $pdf->addPage();
    }

    /**
     * Make an array with hive states
     *
     * @return array<string, int>
     */
    private function getHiveStats(): array
    {
        $returnArray = [];
        $states = HiveState::cases();
        foreach ($states as $state) {
            $name = $state->translate();
            $returnArray[$name] = 0;
        }
        return $returnArray;
    }

    /**
     * Get total weight of harvests
     *
     * @param HarvestDto[] $harvests
     * @return array<string, float>
     */
    private function sortHarvests(array $harvests): array
    {
        $returnArray = [];
        foreach ($harvests as $harvest) {
            if (!key_exists($harvest->type, $returnArray)) {
                $returnArray[$harvest->type] = $harvest->weight;
            } else {
                $returnArray[$harvest->type] += $harvest->weight;
            }
        }
        return $returnArray;
    }

    /**
     * Get total weight of harvests
     *
     * @param HarvestDto[] $harvests
     * @return float
     */
    private function getTotalHarvest(array $harvests): float
    {
        $total = 0;
        foreach ($harvests as $harvest) {
            $total += $harvest->weight;
        }
        return $total;
    }
}
