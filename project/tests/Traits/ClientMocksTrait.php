<?php

namespace App\Tests\Traits;

use App\Domain\Client\Entity\ClientEntityInterface;
use App\Domain\Client\Enum\EducationEnum;
use App\Domain\PhoneOperator\ValueObject\PhoneOperator;
use App\Domain\Profile\Entity\ProfileEntityInterface;
use App\Domain\User\Entity\UserEntityInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

trait ClientMocksTrait
{
    private function createUserMock(TestCase $t, int $id = 1001): UserEntityInterface&MockObject
    {
        $user = $t->createMock(UserEntityInterface::class);
        $user->method('getId')->willReturn($id);

        return $user;
    }

    private function createProfileMock(
        TestCase             $t,
        int                  $profileId = 101,
        ?UserEntityInterface $user = null,
    ): ProfileEntityInterface&MockObject
    {
        $user ??= $this->createUserMock($t);

        $profile = $t->createMock(ProfileEntityInterface::class);
        $profile->method('getId')->willReturn($profileId);
        $profile->method('getUser')->willReturn($user);

        return $profile;
    }

    private function createClientMock(
        TestCase                $t,
        int                     $clientId = 1,
        ?ProfileEntityInterface $profile = null,
    ): ClientEntityInterface&MockObject
    {
        $profile ??= $this->createProfileMock($t);

        $client = $t->createMock(ClientEntityInterface::class);
        $client->method('getId')->willReturn($clientId);
        $client->method('getProfile')->willReturn($profile);

        return $client;
    }

    private function createClientMockComplete(
        TestCase                $t,
        int                     $clientId = 1,
        ?ProfileEntityInterface $profile = null,
    ): ClientEntityInterface&MockObject
    {
        $now = new \DateTimeImmutable();

        $client = $this->createClientMock($t, $clientId, $profile);
        $client->method('getEducation')->willReturn(EducationEnum::Higher);
        $client->method('getConsentPersonalData')->willReturn(true);
        $client->method('getPhoneOperator')->willReturn(PhoneOperator::fromPhoneOperator('operator'));
        $client->method('getScore')->willReturn(123);
        $client->method('getCreatedAt')->willReturn($now);
        $client->method('getUpdatedAt')->willReturn($now);

        return $client;
    }
}
