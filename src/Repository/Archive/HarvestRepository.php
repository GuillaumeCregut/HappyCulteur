<?php

namespace App\Repository\Archive;

use App\Entity\Hive;
use App\Entity\Apiculteur;
use App\Entity\Archive\Harvest;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Harvest>
 */
class HarvestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Harvest::class);
    }

    public function findByHiveAndBeekeeper(Hive $hive, Apiculteur $user): array
    {
        $hiveName = "{$hive->getName()} - {$hive->getIdentification()}";
        return $this->createQueryBuilder('h')
            ->andWhere('h.beekeeper = :user')
            ->andWhere('h.hive= :hive')
            ->setParameter('user', $user)
            ->setParameter('hive', $hiveName)
            ->orderBy('h.date', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
