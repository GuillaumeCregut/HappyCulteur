<?php

namespace App\Repository;

use App\Entity\Apiary;
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

    public function findWithDatalogger()
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
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    //    /**
    //     * @return Hive[] Returns an array of Hive objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('h')
    //            ->andWhere('h.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('h.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Hive
    //    {
    //        return $this->createQueryBuilder('h')
    //            ->andWhere('h.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
