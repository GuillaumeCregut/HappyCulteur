<?php

namespace App\Dto;

use DateTimeImmutable;

class WeightDto implements StatDtoInterface
{
    public ?float $weight;
    public DateTimeImmutable $date;
    public string $type;

    public static function createFromArray(array $values): static
    {
        $dto = new static();
         foreach($values as $key => $value) {
            if(property_exists($dto, $key)) {
                $dto->$key = $value;
            }
        }
        return $dto;
    }

    public function getDate(): string
    {
        return $this->date->format('d/m/Y');
    }

    public function getValue(): mixed
    {
        return $this->weight;
    }

    public function getType():string
    {
        return $this->type;
    }
}