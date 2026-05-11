<?php

namespace App\Dto;

use App\Entity\Hive;
use App\Entity\Apiculteur;
use DateTimeImmutable;

class HarvestDto
{
    public Apiculteur $keekeeper;
    public DateTimeImmutable $date;
    public float $weight;
    public string $type;
    public ?string $picture;
    public ?Hive $hive;
    
    public static function fromArray(array $datas, Apiculteur $user, ?Hive $hive = null): static
    {
        $dto = new static();
        $dto->keekeeper = $user;
        $dto->hive = $hive;
        $dto->date = $datas['date'];
        $dto->weight = $datas['weight'];
        $dto->type = $datas['honeyType'];
        $dto->picture = $datas['picture'];
        return $dto;
    }
}