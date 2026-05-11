<?php

namespace App\Dto;

use App\Entity\Hive;

class DataloggerStatsDto
{
    public Hive $hive;
    public string $startDate;
    public string $endDate; 
    public bool $isDatasFill = false;
    public ?float $averageTempExt = null;
    public ?float $averageTempInt = null;
    public ?float $averageWeight = null;
    public ?float $averageHygroExt = null;
    public ?float $averageHygroInt = null;
    public ?string $graphTemp = null;
    public ?string $graphHygro = null;
    public ?string $graphWeight = null;
}