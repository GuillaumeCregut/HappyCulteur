<?php

namespace App\Validator;

use App\Entity\Visit;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidVisitDiseaseValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        /** @var Visit $value */
        if($value->isDisease() && ($value->getDisease()=== null)) {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath('disease')
                ->addViolation();
        }
    }
}