<?php

namespace App\Service;

use App\Entity\Apiary;
use App\Entity\Apiculteur;
use App\Repository\HiveRepository;

class HiveFinder
{
    public function __construct(private HiveRepository $repo) {}

    public function findhiveByApiaryOwnedByUser(Apiary $apiary, Apiculteur $user): array
    {
       $hives = $this->repo->findByApiaryAndUser($apiary, $user);
       $result = array_map(fn(array $h)=>['id' => $h['id'], 'name' => $h['name']], $hives);
       return $result;
    }

    public function findHiveByIdOwnedByUser(int $hiveId, Apiculteur $user): array
    {
        $returnArray=[];
        $hive = $this->repo->findOneBy(['id'=>$hiveId]);
        $returnArray['error'] = 200;
        if(null === $hive) {
            $returnArray['error'] = 404;
            return $returnArray;
        }
        if($hive->getApiary()->getBeekeeper() !== $user) {
            $returnArray['error'] = 403;
            return $returnArray;
        }
        $returnArray['state'] = $hive->getState()->translate();
        $returnArray['rise'] = $hive->getRiseNumber();
        $returnArray['swarm'] = $hive->getSwarm()->getName();
        return $returnArray;
    }
}