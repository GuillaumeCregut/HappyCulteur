<?php

namespace App\Service\Stats;

use App\Entity\Apiculteur;
use App\Repository\ApiaryRepository;
use App\Repository\HarvestRepository;

class Beekeeper
{
    public function __construct(private ApiaryRepository $apiaryRepo, private HarvestRepository $harvestRepo) {}

    public function getBeekeeperStats(Apiculteur $user): array
    {
        $returnArray = [];
        $returnArray['apiaries'] = $this->apiaryRepo->findApiaryHiveCountbyBeekeeper($user);
        $returnArray['harvests'] = $this->harvestRepo->findHarvestsByTypeByBeekeeper($user);
        return $returnArray;
    }
}
