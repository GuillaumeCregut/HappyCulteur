<?php

namespace App\Repository\Archive;

use App\Dto\HygrometryDto;
use App\Dto\TempDto;
use App\Dto\WeightDto;
use App\Entity\Hive;
use App\Entity\Apiculteur;
use App\Entity\Archive\Visit;
use DateTimeImmutable;
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

    public function findTempForStats(Hive $hive, Apiculteur $user, ?DateTimeImmutable $from = null, ?DateTimeImmutable $to = null): array
    {
        $hiveName = "{$hive->getName()} - {$hive->getIdentification()}";
        $qb = $this->createQueryBuilder('arv')
            ->select('arv.date', 'arv.temperature', "'archive' as type")
            ->where('arv.beekeeper = :user')
            ->setParameter('user', $user)
            ->andWhere('arv.hive = :hive')
            ->setParameter('hive', $hiveName)
            ->orderBy('arv.date', 'ASC');
        if (null !== $from) {
            $qb->andWhere('arv.date >= :from')
                ->setParameter('from', $from);
        }
        if (null !== $from) {
            $qb->andWhere('arv.date <= :to')
                ->setParameter('to', $to);
        }
        $result = $qb->getQuery()->getResult();
        $returnArray = $this->makeDto($result, TempDto::class);
        return $returnArray;
    }

    public function findHygroForStats(Hive $hive, Apiculteur $user, ?DateTimeImmutable $from = null, ?DateTimeImmutable $to = null): array
    {
        $hiveName = "{$hive->getName()} - {$hive->getIdentification()}";
        $qb = $this->createQueryBuilder('arv')
            ->select('arv.date', 'arv.hygrometry', "'archive' as type")
            ->where('arv.beekeeper = :user')
            ->setParameter('user', $user)
            ->andWhere('arv.hive = :hive')
            ->setParameter('hive', $hiveName)
            ->orderBy('arv.date', 'ASC');
        if (null !== $from) {
            $qb->andWhere('arv.date >= :from')
                ->setParameter('from', $from);
        }
        if (null !== $from) {
            $qb->andWhere('arv.date <= :to')
                ->setParameter('to', $to);
        }
        $result = $qb->getQuery()->getResult();
        $returnArray = $this->makeDto($result, HygrometryDto::class);
        return $returnArray;
    }

    public function findWeightForStats(Hive $hive, Apiculteur $user, ?DateTimeImmutable $from = null, ?DateTimeImmutable $to = null): array
    {
        $hiveName = "{$hive->getName()} - {$hive->getIdentification()}";
        $qb = $this->createQueryBuilder('arv')
            ->select('arv.date', 'arv.weight', "'archive' as type")
            ->where('arv.beekeeper = :user')
            ->setParameter('user', $user)
            ->andWhere('arv.hive = :hive')
            ->setParameter('hive', $hiveName)
            ->orderBy('arv.date', 'ASC');
        if (null !== $from) {
            $qb->andWhere('arv.date >= :from')
                ->setParameter('from', $from);
        }
        if (null !== $from) {
            $qb->andWhere('arv.date <= :to')
                ->setParameter('to', $to);
        }
        $result = $qb->getQuery()->getResult();
        $returnArray = $this->makeDto($result, WeightDto::class);
        return $returnArray;
    }

    private function makeDto(array $results, string $dto): array
    {
        $returnArray = [];
        foreach ($results as $result) {
            $newDto = $dto::createFromArray($result);
            $returnArray[] = $newDto;
        }
        return $returnArray;
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
