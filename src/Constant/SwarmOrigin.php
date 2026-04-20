<?php

namespace App\Constant;

enum SwarmOrigin: int
{
    case BUY = 0;
    case GIVEN = 1;
    case PICKUP = 2;
    case SWARMING = 3;

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
