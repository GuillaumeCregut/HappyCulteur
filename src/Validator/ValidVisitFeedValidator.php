<?php

namespace App\Validator;


use App\Entity\Visit;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidVisitFeedValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        /** @var Visit $value */
        if($value->isFeeded() && ($value->getFeeding()=== null)) {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath('feeding')
                ->addViolation();
        }
    }
}