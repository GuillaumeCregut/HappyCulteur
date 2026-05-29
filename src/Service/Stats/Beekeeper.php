<?php

namespace App\Service\Stats;

use App\Entity\Apiculteur;
use App\Repository\ApiaryRepository;
use App\Repository\Archive\HarvestRepository as ArchiveHarvestRepository;
use App\Repository\HarvestRepository;

class Beekeeper
{
    public function __construct(
        private ApiaryRepository $apiaryRepo,
        private HarvestRepository $harvestRepo,
        private ArchiveHarvestRepository $archives
    ) {}

    public function getBeekeeperStats(Apiculteur $user): array
    {
        $returnArray = [];
        $returnArray['apiaries'] = $this->apiaryRepo->findApiaryHiveCountbyBeekeeper($user);
        $harvests = $this->harvestRepo->findHarvestsByTypeByBeekeeper($user);
        $archivesHarvests = $this->archives->findHarvestsByTypeByBeekeeper($user);
        $harvests = $this->mergeHarvests($harvests, $archivesHarvests);
        $returnArray['harvests'] = $harvests;
        return $returnArray;
    }

    private function mergeHarvests(array $harvests, array $archives): array
    {
        $returnArray = [];
        foreach (array_merge($harvests, $archives) as $item) {
            $name = $item['name'];
            if (isset($returnArray[$name])) {
                $returnArray[$name]['weight'] += $item['weight'];
            } else {
                $returnArray[$name] = [
                    'name' => $name,
                    'weight' => $item['weight']
                ];
            }
        }
        return array_values($returnArray);
    }
}
