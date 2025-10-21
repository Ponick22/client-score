<?php

namespace App\Application\Client\Connector\Command\ScoreCalculate\DTO;

use App\Application\Client\DTO\ClientOutputData;
use App\Application\Client\DTO\ClientScoringData;

readonly class OutputData
{
    public function __construct(
        private ClientOutputData  $clientOutputData,
        private ClientScoringData $clientScoringData,
    ) {}

    public function getClientOutputData(): ClientOutputData
    {
        return $this->clientOutputData;
    }

    public function getClientScoringData(): ClientScoringData
    {
        return $this->clientScoringData;
    }
}
