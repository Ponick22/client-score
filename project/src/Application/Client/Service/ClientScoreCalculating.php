<?php

namespace App\Application\Client\Service;

use App\Application\Client\DTO\ClientScoringData;
use App\Application\Scoring\Contract\ScoringInterface;
use App\Application\Scoring\Types\ConsentScoring;
use App\Application\Scoring\Types\EducationScoring;
use App\Application\Scoring\Types\EmailDomainScoring;
use App\Application\Scoring\Types\PhoneOperatorScoring;
use App\Application\Scoring\ValueObject\ScoringDataCollection;
use App\Domain\Client\Entity\ClientEntityInterface;

readonly class ClientScoreCalculating
{
    /**
     * @param ScoringInterface[] $scoringServices
     */
    public function __construct(private iterable $scoringServices)
    {
        foreach ($scoringServices as $scoringService) {
            if (!$scoringService instanceof ScoringInterface) {
                throw new \LogicException(sprintf("Expected %s", $scoringService::class));
            }
        }
    }

    public function calculate(ClientEntityInterface $client): ClientScoringData
    {
        $score   = 0;
        $details = new ScoringDataCollection();

        foreach ($this->scoringServices as $scoringService) {
            $value = match (true) {
                $scoringService instanceof ConsentScoring       => $client->getConsentPersonalData(),
                $scoringService instanceof EducationScoring     => $client->getEducation()->value,
                $scoringService instanceof EmailDomainScoring   => (string)$client->getProfile()->getEmail(),
                $scoringService instanceof PhoneOperatorScoring => (string)$client->getPhoneOperator(),
                default                                         => null,
            };

            if ($value === null || $value === '') {
                continue;
            }

            $data = $scoringService->scoring($value);

            $score += $data->getScore();
            $details->add($data);
        }

        return new ClientScoringData(
            $score,
            $details
        );
    }
}
