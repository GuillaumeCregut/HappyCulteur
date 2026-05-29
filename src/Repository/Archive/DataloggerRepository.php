<?php

namespace App\Repository\Archive;

use App\Entity\Hive;
use DateTimeImmutable;
use App\Entity\Apiculteur;
use App\Entity\Archive\Datalogger;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Datalogger>
 */
class DataloggerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Datalogger::class);
    }

    public function findByBeekeeper(Apiculteur $user): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.beekeeper = :user')
            ->setParameter('user', $user)
            ->orderBy('d.hive', 'ASC')
            ->orderBy('d.dateTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByHiveAndBeekeeper(Hive $hive, Apiculteur $user): array
    {
        $hiveName = "{$hive->getName()} - {$hive->getIdentification()}";
        return $this->createQueryBuilder('d')
            ->andWhere('d.beekeeper = :user')
            ->andWhere('d.hive= :hive')
            ->setParameter('user', $user)
            ->setParameter('hive', $hiveName)
            ->orderBy('d.dateTime', 'ASC')
            ->orderBy('d.hive', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByHiveBetweenDates(
        Hive $hive,
        Apiculteur $user,
        ?DateTimeImmutable $start = null,
        ?DateTimeImmutable $end = null
    ): array {
        $hiveName = "{$hive->getName()} - {$hive->getIdentification()}";
        $qb = $this->createQueryBuilder('d')
            ->where('d.hive = :hive')
            ->andWhere('d.beekeeper = :user')
            ->setParameter('hive', $hiveName)
            ->setParameter('user', $user)
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

    //    /**
    //     * @return Datalogger[] Returns an array of Datalogger objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

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
