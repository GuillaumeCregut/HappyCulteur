<?php

namespace App\Constant;

enum Weather: int
{
    const CLOUDY = 1;
    const RAIN = 2;
    const SNOW = 3;
    const SUNNY = 4;

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