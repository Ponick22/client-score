<?php

namespace App\Tests\Unit\Application\Client\DTO;

use App\Application\Client\DTO\ClientOutputData;
use App\Domain\Client\Enum\EducationEnum;
use App\Domain\PhoneOperator\ValueObject\PhoneOperator;
use App\Tests\Traits\ClientMocksTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ClientOutputDataTest extends KernelTestCase
{
    use ClientMocksTrait;

    public function testConstructAndGetters(): void
    {
        $id        = 1;
        $profileId = 2;
        $userId    = 3;

        $education = EducationEnum::Special;
        $now       = new \DateTimeImmutable();
        $consent   = true;
        $operator  = PhoneOperator::fromPhoneOperator('operator');
        $score     = 123;

        $userMock    = $this->createUserMock($this, $userId);
        $profileMock = $this->createProfileMock($this, $profileId, $userMock);
        $entityMock  = $this->createClientMock($this, $id, $profileMock);

        $entityMock->method('getEducation')->willReturn($education);
        $entityMock->method('getConsentPersonalData')->willReturn($consent);
        $entityMock->method('getPhoneOperator')->willReturn($operator);
        $entityMock->method('getScore')->willReturn($score);
        $entityMock->method('getCreatedAt')->willReturn($now);
        $entityMock->method('getUpdatedAt')->willReturn($now);

        $dto = new ClientOutputData($entityMock);

        $this->assertSame($id, $dto->getId());
        $this->assertSame($profileId, $dto->getProfile()->getId());
        $this->assertSame($userId, $dto->getProfile()->getUserId());
        $this->assertSame($education, $dto->getEducation());
        $this->assertSame($consent, $dto->getConsentPersonalData());
        $this->assertSame((string)$operator, $dto->getPhoneOperator());
        $this->assertSame($score, $dto->getScore());
        $this->assertSame($now, $dto->getCreatedAt());
        $this->assertSame($now, $dto->getUpdatedAt());
    }
}
