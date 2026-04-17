<?php

namespace App\Constant;

enum Weather: int
{
    case CLOUDY = 1;
    case RAIN = 2;
    case SNOW = 3;
    case SUNNY = 4;

    public function label(): string
    {
        return match($this) {
            self::CLOUDY => 'Nuageux',
            self::RAIN => 'Pluie',
            self::SNOW => 'Neige',
            self::SUNNY => 'Ensoleillé',
        };
    }
}