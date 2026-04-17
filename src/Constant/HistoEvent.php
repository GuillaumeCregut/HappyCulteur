<?php

namespace App\Constant;

enum HistoEvent: int
{
    case ADD = 0;
    case UPDATE = 1;
    
    public function label(): string
    {
        return match ($this) {
            self::ADD => 'Ajout',
            self::UPDATE => 'Modification'
        };
    }
}
