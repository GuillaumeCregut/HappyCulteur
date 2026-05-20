<?php

namespace App\Service\Stats;

use App\Entity\Apiculteur;
use App\Repository\ApiaryRepository;

class Beekeeper
{
    public function __construct(private ApiaryRepository $repo) {}

    public function getBeekeeperStats(Apiculteur $user): array
    {
        $returnArray = [];
        $returnArray['apiaries'] = $this->repo->findApiaryHiveCountbyBeekeeper($user);
        return $returnArray;
    }
}
