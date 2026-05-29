<?php

namespace App\Dto;

use DateTimeImmutable;
use App\Entity\Datalogger;
use App\Entity\Archive\Datalogger as ArchiveDatalogger;

class DataloggerDto
{
    public DateTimeImmutable $dateTime;
    public ?float $weight = null;
    public ?float $extTemp = null;
    public ?float $intTemp = null;
    public ?int $intHygro = null;
    public ?int $extHygro = null;

    public static function fromLogs(Datalogger $log): static
    {
        $dto = new static();
        $dto->extHygro = $log->getExtHyrgo();
        $dto->extTemp = $log->getExtTemp();
        $dto->dateTime = $log->getDateTime();
        $dto->weight = $log->getWeight();
        $dto->intTemp = $log->getIntTemp();
        $dto->intHygro = $log->getIntHygro();
        return $dto;
    }

    public static function fromArchive(ArchiveDatalogger $log): static
    {
        $dto = new static();
        $dto->extHygro = $log->getExtHygro();
        $dto->extTemp = $log->getExtTemp();
        $dto->dateTime = $log->getDateTime();
        $dto->weight = $log->getWeight();
        $dto->intTemp = $log->getIntTemp();
        $dto->intHygro = $log->getIntHygro();
        return $dto;
    }
}
