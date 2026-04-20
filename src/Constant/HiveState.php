<?php

namespace App\Constant;

enum HiveState: int
{
    case ACTIVE_HIVE = 0;
    case DEAD_HIVE = 1;
    case STOCK_HIVE = 2;


    public function label(): string
    {
        return match ($this) {
        self::ACTIVE_HIVE => '-actif',
        self::DEAD_HIVE => '-dead',
        self::STOCK_HIVE =>'-stock'
        };
    }
}