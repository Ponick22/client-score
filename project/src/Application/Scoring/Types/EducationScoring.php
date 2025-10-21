<?php

namespace App\Application\Scoring\Types;

use App\Application\Scoring\Contract\ScoringAbstract;

readonly class EducationScoring extends ScoringAbstract
{
    public function getName(): string
    {
        return 'education';
    }
}
