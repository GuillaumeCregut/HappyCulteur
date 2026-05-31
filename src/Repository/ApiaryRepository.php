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
        $owned = $this->createQueryBuilder('a')
            ->where('a.owner = :user')
            ->setParameter('user', $beekeeper)
            ->getQuery()
            ->getResult();
        $shared = $this->createQueryBuilder('a')
            ->join('a.beekeepers', 'u')
            ->where('u = :user')
            ->setParameter('user', $beekeeper)
            ->getQuery()
            ->getResult();
        $merged = array_merge($owned, $shared);

        $unique = [];
        foreach ($merged as $entity) {
            $unique[$entity->getId()] = $entity;
        }
        return array_values($unique);
    }

    public function findApiaryHiveCountbyBeekeeper(Apiculteur $user): array
    {
        $qb = $this->createQueryBuilder('ap')
            ->select('ap.name', 'COUNT(hv.id) AS hiveCount')
            ->leftJoin('ap.hives', 'hv')
            ->where('ap.owner = :beekeeper')
            ->setParameter('beekeeper', $user)
            ->groupBy('ap.id')
            ->orderBy('ap.name', 'ASC');
        return $qb->getQuery()->getResult();
    }

    public function findByBeekeeperInShared(Apiculteur $user): array
    {
        return $this->createQueryBuilder('a')
            ->join('a.beekeepers', 'b')
            ->where('b = :beekeeper')
            ->setParameter('beekeeper', $user)
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
