<?php

namespace App\Repository;

use App\Dto\StatDtoInterface;
use App\Dto\TempDto;
use App\Entity\Hive;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;

class StatsDataRepository
{
    public function __construct(private Connection $connection) {}

    public function findTempsData(
        Hive $hive,
        ?DateTimeImmutable $start = null,
        ?DateTimeImmutable $end = null,
    ): array {
        $params = ['hiveId' => $hive->getId()];
        $dateConditionVisit = '';
        $dateConditionDlog  = '';

        if ($start !== null) {
            $dateConditionVisit .= ' AND v.date >= :start';
            $dateConditionDlog  .= ' AND d.date_time >= :start';
            $params['start'] = $start->format('Y-m-d');
        }

        if ($end !== null) {
            $dateConditionVisit .= ' AND v.date <= :end';
            $dateConditionDlog  .= ' AND d.date_time <= :end';
            $params['end'] = $end->format('Y-m-d');
        }

        $sqlVisit = "SELECT v.temperature as temperature, v.date as date, 'visit' as type
        FROM hive h
        LEFT JOIN visit v ON v.hive_id = h.id
        WHERE h.id = :hiveId {$dateConditionVisit} AND v.temperature IS NOT NULL ORDER BY v.date ASC";

        $sqlDataLogger = "SELECT d.ext_temp as temperature, d.date_time as date, 'datalogger' as type 
        FROM hive h
        LEFT JOIN datalogger d ON d.hive_id = h.id
        WHERE h.id = :hiveId {$dateConditionDlog} AND d.ext_temp IS NOT NULL ORDER BY d.date_time ASC";

        $visits = $this->execQuery($sqlVisit, $params);
        $datalogger = $this->execQuery($sqlDataLogger, $params);

        $values = $this->formatDate(array_merge($visits, $datalogger));
        $valuesDto = $this->MakeDto($values, TempDto::class);
        return $valuesDto;
    }

    private function execQuery(string $query, array $params): array
    {
        $datas = $this->connection->executeQuery($query, $params)->fetchAllAssociative();
        return $datas;
    }

    private function MakeDto(array $values, string $dto): array
    {
        $returnArray = [];
        if (!is_subclass_of($dto, StatDtoInterface::class)) {
            throw new \InvalidArgumentException(
                sprintf('%s must implement DtoCallable', $dto)
            );
        }
        /**@var StatDtoInterface $dto */
        foreach ($values as $value) {
            $newDto = $dto::createFromArray($value);
            $returnArray[] = $newDto;
        }
        return  $returnArray;
    }

    private function formatDate(array $values): array
    {
        $returnArray = [];
        foreach ($values as $row) {
            $newValueArray = [];
            foreach ($row as $name => $value) {
                $newValueArray[$name] = $value;
                if ($name === 'date') {
                    $newValueArray[$name] = new DateTimeImmutable($value);
                }
            }
            $returnArray[] = $newValueArray;
        }
        return $returnArray;
    }
}
