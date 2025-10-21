<?php

namespace App\Application\Client;

use App\Application\Client\DTO\ClientUpdateData;
use App\Application\Client\Service\ClientPhoneOperatorChanger;
use App\Application\Client\Service\ClientScoreCalculating;
use App\Application\PhoneOperator\Exception\PhoneOperatorException;
use App\Domain\Client\Entity\ClientEntityInterface;

readonly class ClientEntityUpdateService
{
    public function __construct(
        private ClientPhoneOperatorChanger $phoneOperatorChanger,
        private ClientScoreCalculating     $scoreCalculating,
    ) {}

    /**
     * @throws PhoneOperatorException
     */
    public function update(ClientEntityInterface $entity, ClientUpdateData $data): ClientEntityInterface
    {
        $entity
            ->setEducation($data->getEducation())
            ->setConsentPersonalData($data->getConsentPersonalData());

        if ($data->getProfilePhone()) {
            $this->phoneOperatorChanger->change($entity, $data->getProfilePhone());
        }

        $entity->setScore($this->scoreCalculating->calculate($entity)->getScore());

        return $entity;
    }
}
