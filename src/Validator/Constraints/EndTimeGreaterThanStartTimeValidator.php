<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class EndTimeGreaterThanStartTimeValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof EndTimeGreaterThanStartTime) {
            throw new UnexpectedTypeException($constraint, EndTimeGreaterThanStartTime::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        /** @var TimeEntry $timeEntry */
        $timeEntry = $this->context->getObject();

        if ($timeEntry->getEndTime() <= $timeEntry->getStartTime()) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
