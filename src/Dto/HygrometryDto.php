<?php

namespace App\Dto;

use DateTimeImmutable;

class HygrometryDto implements StatDtoInterface
{
    public ?int $hygrometry;
    public DateTimeImmutable $date;
    public string $type;

    public static function createFromArray(array $values): static
    {
        $dto = new static();
        foreach ($values as $key => $value) {
            if (property_exists($dto, $key)) {
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
        return $this->hygrometry;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
