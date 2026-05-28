<?php

namespace App\Repository\Archive;

use App\Entity\Hive;
use DateTimeImmutable;
use App\Dto\HarvestDto;
use App\Entity\Apiculteur;
use App\Entity\Archive\Harvest;
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

    public function findByHiveAndBeekeeper(Hive $hive, Apiculteur $user): array
    {
        $hiveName = "{$hive->getName()} - {$hive->getIdentification()}";
        return $this->createQueryBuilder('h')
            ->andWhere('h.beekeeper = :user')
            ->andWhere('h.hive= :hive')
            ->setParameter('user', $user)
            ->setParameter('hive', $hiveName)
            ->orderBy('h.date', 'ASC')
            ->getQuery()
            ->getResult();
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
        $hiveName = "{$hive->getName()} - {$hive->getIdentification()}";
        $qb = $this->createQueryBuilder('h')
            ->select(
                'h.date',
                'h.weight',
                'h.honeyKind as honeyType',
                'h.picture as picture',
            )
            ->andWhere('h.beekeeper = :beekeeper')
            ->setParameter('beekeeper', $user)
            ->andWhere('h.hive = :hive')
            ->setParameter('hive', $hiveName)
            ->orderBy('h.date', 'ASC');
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

    public function findHarvestsByTypeByBeekeeper(Apiculteur $user): array
    {
        $qb = $this->createQueryBuilder('ha')
            ->select('SUM(ha.weight) as weight', 'ha.honeyKind as name')
            ->where('ha.beekeeper = :beekeeper')
            ->groupBy('ha.honeyKind')
            ->orderBy('ha.honeyKind')
            ->setParameter('beekeeper', $user);
        return $qb->getQuery()->getResult();
    }
}
