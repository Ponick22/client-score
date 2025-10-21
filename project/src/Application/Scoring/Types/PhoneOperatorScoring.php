<?php

namespace App\Application\Scoring\Types;

use App\Application\Scoring\Contract\ScoringAbstract;

readonly class PhoneOperatorScoring extends ScoringAbstract
{
    public function getName(): string
    {
        return 'phone_operator';
    }
}
