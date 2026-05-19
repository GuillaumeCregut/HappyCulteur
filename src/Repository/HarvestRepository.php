<?php

namespace App\Repository;

use App\Entity\Hive;
use DateTimeImmutable;
use App\Dto\HarvestDto;
use App\Entity\Apiary;
use App\Entity\Harvest;
use App\Entity\Apiculteur;
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

    /**
     * @return HarvestDto[] Returns an array of Harvest objects
     */
    public function findByHiveBeekeeperDate(
        Apiculteur $user,
        ?Hive $hive,
        ?DateTimeImmutable $start = null,
        ?DateTimeImmutable $end = null
    ): array {
        $qb = $this->createQueryBuilder('h')
            ->select(
                'h.date',
                'h.weight',
                'ho.name as honeyType',
                'ho.picture as picture',
            )
            ->join('h.honeyKind', 'ho')
            ->join('h.beekeeper', 'bk')
            ->where('bk = :beeKeeper')
            ->setParameter('beeKeeper', $user)
            ->orderBy('h.date', 'ASC');
        if (null !== $hive) {
            $qb->join('h.hive', 'hv')
                ->andWhere('hv = :hive')
                ->setParameter('hive', $hive);
        }
        if (null !== $start) {
            $qb->andWhere('d.date >= :startDate')
                ->setParameter('startDate', $start);
        }

        if (null !== $end) {
            $qb->andWhere('d.date <= :endDate')
                ->setParameter('endDate', $end);
        }
        $returnArray = [];
        $results = $qb->getQuery()->getResult();
        foreach ($results as $result) {
            $dto = HarvestDto::fromArray($result, $user, $hive);
            $returnArray[] = $dto;
        }
        return $returnArray;
    }

    public function findHarvestsByApiary(Apiary $apiary, Apiculteur $user): array
    {
        $qb = $this->createQueryBuilder('h')
            ->select(
                'h.date',
                'h.weight',
                'ho.name as honeyType',
                'ho.picture as picture',
            )
            ->join('h.hive', 'hv')
            ->join('h.honeyKind', 'ho')
            ->join('hv.apiary', 'ap')
            ->where('ap = :apiary')
            ->andWhere('h.beekeeper = :beekeeper')
            ->setParameter('apiary', $apiary)
            ->setParameter('beekeeper', $user)
            ->orderBy('h.date', 'ASC')
            ->getQuery();
        $results = $qb->getResult();
        $returnArray = [];
        foreach ($results as $result) {
            $dto = HarvestDto::fromArray($result, $user);
            $returnArray[] = $dto;
        }
        return $returnArray;
    }

    //    public function findOneBySomeField($value): ?Harvest
    //    {
    //        return $this->createQueryBuilder('h')
    //            ->andWhere('h.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
