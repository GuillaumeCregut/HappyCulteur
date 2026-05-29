<?php

namespace App\Repository\Archive;

use App\Entity\Hive;
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
