<?php

namespace App\Constant;

enum HiveState: int
{
    const ACTIVE_HIVE = 0;
    const DEAD_HIVE = 1;
    const STOCK_HIVE = 2;


    public function label(): string
    {
        return match ($this) {
        self::ACTIVE_HIVE => '_actif',
        self::DEAD_HIVE => '_dead',
        self::STOCK_HIVE =>'_stock'
        };
    }
}