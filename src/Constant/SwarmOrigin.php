<?php

namespace App\Constant;

enum SwarmOrigin: int
{
    const BUY = 0;
    const GIVEN = 1;
    const PICKUP = 2;
    const SWARMING = 3;

    public function label(): string
    {
        return match ($this) {
            self::BUY => 'Achat',
            self::GIVEN => 'Don',
            self::PICKUP => 'Prélèvement',
            self::SWARMING => 'Essaimage'
        };
    }
}
