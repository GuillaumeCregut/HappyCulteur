<?php

namespace App\Dto;

interface StatDtoInterface
{
    public static function createFromArray(array $values): static;
    public function getDate(): string;
    public function getValue(): mixed;
    public function getType(): string;
}
