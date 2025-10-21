<?php

namespace App\Application\Profile\Service;

use App\Domain\Profile\Entity\ProfileEntityInterface;

readonly class ProfilePhoneChanger
{
    public function __construct(
        private ProfilePhoneNormalizer $normalizer
    ) {}

    public function change(ProfileEntityInterface $entity, string $phone): void
    {
        $entity->changePhone($this->normalizer->normalize($phone));
    }
}
