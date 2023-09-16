<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class EndTimeGreaterThanStartTime extends Constraint
{
    public string $message = 'The end time should be greater than the start time.';
}
