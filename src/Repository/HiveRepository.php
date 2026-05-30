<?php

namespace App\Repository;

use App\Entity\Apiary;
use App\Entity\Apiculteur;
use App\Entity\Hive;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Hive>
 */
class HiveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hive::class);
    }

    public function findWithDatalogger(): array
    {
        return $this->createQueryBuilder('h')
            ->where('h.dataLoggerName IS NOT NULL')
            ->getQuery()
            ->getResult();
    }

    public function findByApiary(Apiary $value): array
    {
        return $this->createQueryBuilder('h')
            ->select('h.id, h.name, h.coordX, h.coordY, h.coordZ')
            ->andWhere('h.apiary = :val')
            ->andWhere('h.coordX IS NOT NULL')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByApiaryAndUser(Apiary $apiary, Apiculteur $user): array
    {
        return $this->createQueryBuilder('h')
            ->select('h.id, h.name')
            ->andWhere('h.apiary = :val')
            ->andWhere('h.beekeeper = :beekeeper')
            ->setParameter('val', $apiary)
            ->setParameter('beekeeper', $user)
            ->orderBy('h.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByStock(Apiculteur $user): array
    {
        return $this->createQueryBuilder('h')
            ->where('h.apiary IS NULL')
            ->andWhere('h.beekeeper = :beekeeper')
            ->setParameter('beekeeper', $user)
            ->getQuery()
            ->getResult();
    }

    public function findHiveByApiaryAndOwner(Apiary $apiary, Apiculteur $user): array
    {
        return $this->createQueryBuilder('h')
            ->where('h.apiary = :apiary')
            ->andWhere('h.beekeeper = :beekeeper')
            ->setParameter('apiary', $apiary)
            ->setParameter('beekeeper', $user)
            ->getQuery()
            ->getResult();
    }

    public function findHivesByApiariesNotOwner(Apiary $apiary, Apiculteur $user): array
    {
        return $this->createQueryBuilder('h')
            ->where('h.apiary = :apiary')
            ->andWhere('h.beekeeper <> :beekeeper')
            ->setParameter('apiary', $apiary)
            ->setParameter('beekeeper', $user)
            ->getQuery()
            ->getResult();
    }

    public function findHivesForApiaryDisplay(Apiary $apiary): array
    {
        $qb = $this->createQueryBuilder('h')
            ->select('h.name', 'h.id', 'h.state', 'k.picture', 'b.name as beekeeperName', 'b.firstname  as beekeeperFirstname', 'b.id as beekeeper',)
            ->join('h.beekeeper', 'b')
            ->join('h.kind', 'k')
            ->where('h.apiary = :apiary')
            ->setParameter('apiary', $apiary);
        return $qb->getQuery()->getResult();
    }
}
