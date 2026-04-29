<?php

namespace App\Repository;

use App\Entity\Apiary;
use App\Entity\Apiculteur;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Apiary>
 */
class ApiaryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Apiary::class);
    }

    public function findByBeekeeper(Apiculteur $beekeeper): array
    {
        return $this->createQueryBuilder('a')
            ->Where('a.beekeeper = :beekeeper')
            ->setParameter('beekeeper', $beekeeper)
            ->orderBy('a.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Apiary[] Returns an array of Apiary objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Apiary
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
