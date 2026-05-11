<?php

namespace App\Dto;

class HarvestHiveDto
{
    public string $startDate;
    public string $endDate;
    public bool $isFilled = false;
    public array $harvests = [];
    public float $totalWeight = 0;
    public ?string $file = null;
}
