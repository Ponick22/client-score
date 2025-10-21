<?php

namespace App\Application\Client\DTO;

use App\Application\Scoring\ValueObject\ScoringDataCollection;

readonly class ClientScoringData
{
    public function __construct(
        private int                   $score,
        private ScoringDataCollection $scoreDetails
    ) {}

    public function getScore(): int
    {
        return $this->score;
    }

    public function getScoreDetails(): ScoringDataCollection
    {
        return $this->scoreDetails;
    }
}
