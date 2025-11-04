<?php

namespace App\Application\Scoring\Types;

use App\Application\Scoring\Contract\ScoringAbstract;
use App\Application\Scoring\DTO\ScoringData;

readonly class ConsentScoring extends ScoringAbstract
{
    public function getName(): string
    {
        return 'consent';
    }

    public function scoring(string $value): ScoringData
    {
        $value = $value ? 'true' : 'false';

        return parent::scoring($value);
    }
}
