<?php

namespace App\Repository\Archive;

use App\Entity\Hive;
use App\Entity\Apiculteur;
use App\Entity\Archive\Visit;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Visit>
 */
class VisitsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Visit::class);
    }

    public function findByHiveAndBeekeeper(Hive $hive, Apiculteur $user): array
    {
        $hiveName = "{$hive->getName()} - {$hive->getIdentification()}";
        return $this->createQueryBuilder('v')
            ->andWhere('v.beekeeper = :user')
            ->andWhere('v.hive= :hive')
            ->setParameter('user', $user)
            ->setParameter('hive', $hiveName)
            ->orderBy('v.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Visits[] Returns an array of Visits objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Visits
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
