<?php

namespace App\Constant;

enum HistoEvent: int
{
    const ADD = 0;
    const UPDATE = 1;
    
    public function label(): string
    {
        return match ($this) {
            self::ADD => 'Ajout',
            self::UPDATE => 'Modification'
        };
    }
}
