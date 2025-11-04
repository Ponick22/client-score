<?php

namespace App\Application\Scoring\Types;

use App\Application\Scoring\Contract\ScoringAbstract;
use App\Application\Scoring\DTO\ScoringData;

readonly class EmailDomainScoring extends ScoringAbstract
{
    public function getName(): string
    {
        return 'email_domain';
    }

    public function scoring(string $value): ScoringData
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $value = substr(strrchr($value, "@"), 1);
        }

        return parent::scoring(mb_strtolower($value));
    }
}
