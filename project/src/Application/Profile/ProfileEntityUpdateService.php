<?php

namespace App\Application\Profile;

use App\Application\Profile\DTO\ProfileUpdateData;
use App\Application\Profile\Service\ProfilePhoneChanger;
use App\Domain\Profile\Entity\ProfileEntityInterface;

readonly class ProfileEntityUpdateService
{
    public function __construct(
        private ProfilePhoneChanger $phoneChanger,
    ) {}

    public function update(ProfileEntityInterface $entity, ProfileUpdateData $data): ProfileEntityInterface
    {
        $entity
            ->setEmail($data->getEmail())
            ->setFirstName($data->getFirstName())
            ->setLastName($data->getLastName());

        $this->phoneChanger->change($entity, $data->getPhone());

        return $entity;
    }
}
