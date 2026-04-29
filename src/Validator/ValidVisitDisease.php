<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ValidVisitDisease extends Constraint
{
    public string $message = "Veuillez remplir le champs si la case est cochée";

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
