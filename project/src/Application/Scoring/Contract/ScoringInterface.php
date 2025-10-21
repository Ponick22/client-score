<?php

namespace App\Application\Scoring\Contract;

use App\Application\Scoring\DTO\ScoringData;

interface ScoringInterface
{
    public function getName(): string;
    public function scoring(string $value): ScoringData;
}
