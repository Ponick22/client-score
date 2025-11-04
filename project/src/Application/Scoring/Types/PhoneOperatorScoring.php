<?php

namespace App\Application\Scoring\Types;

use App\Application\Scoring\Contract\ScoringAbstract;
use App\Application\Scoring\DTO\ScoringData;

readonly class PhoneOperatorScoring extends ScoringAbstract
{
    public function getName(): string
    {
        return 'phone_operator';
    }

    public function scoring(string $value): ScoringData
    {
        $value = mb_strtolower($value);

        return parent::scoring($value);
    }
}
