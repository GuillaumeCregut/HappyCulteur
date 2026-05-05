<?php

namespace App\Repository;

use App\Entity\Hive;
use App\Entity\Visit;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Visit>
 */
class VisitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Visit::class);
    }

    public function findByhiveAndDates(
        Hive $hive, 
        DateTimeImmutable $endDate,
        ?DateTimeImmutable $startDate = null,
    ): array {
        $query =  $this->createQueryBuilder('v')
            ->where('v.hive = :hive')
            ->andWhere('v.date <= :endDate')
            ->setParameter('hive', $hive)
            ->setParameter('endDate', $endDate)
            ->orderBy('v.date', 'ASC');
        if(null !== $startDate) {
            $query->andWhere('v.date>= :startDate')
                ->setParameter('startDate', $startDate);
        }
        return $query->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Visit[] Returns an array of Visit objects
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

    //    public function findOneBySomeField($value): ?Visit
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
