<?php

namespace App\Repository;

use App\Entity\Apiculteur;
use App\Entity\Datalogger;
use App\Entity\Hive;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Datalogger>
 */
class DataloggerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Datalogger::class);
    }

    public function findByHiveBetweenDates(
        Hive $hive,
        ?DateTimeImmutable $start = null,
        ?DateTimeImmutable $end = null
    ): array {
        $qb = $this->createQueryBuilder('d')
            ->where('d.hive = :hive')
            ->setParameter('hive', $hive)
            ->orderBy('d.dateTime', 'ASC');

        if ($start !== null) {
            $qb->andWhere('d.date >= :startDate')
                ->setParameter('startDate', $start);
        }

        if ($end !== null) {
            $qb->andWhere('d.date <= :endDate')
                ->setParameter('endDate', $end);
        }
        return $qb->getQuery()->getResult();
    }

    public function findByHiveAndBeekeeper(
        Hive $hive,
        Apiculteur $user
    ): array {
        $qb = $this->createQueryBuilder('d')
            ->where('d.hive = :hive')
            ->andWhere('d.beekeeper = :bk')
            ->setParameter('hive', $hive)
            ->setParameter('bk', $user)
            ->orderBy('d.dateTime', 'ASC');
        return $qb->getQuery()->getResult();
    }

    /**
     * @return Datalogger[] Returns an array of Datalogger objects
     */
    public function findByBeekeeper(Apiculteur $user): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.beekeeper = :val')
            ->setParameter('val', $user)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    //    public function findOneBySomeField($value): ?Datalogger
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
