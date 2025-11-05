<?php

namespace App\Tests\Unit\Application\Profile\DTO;

use App\Application\Profile\DTO\ProfileOutputData;
use App\Domain\Profile\Entity\ProfileEntityInterface;
use App\Domain\User\Entity\UserEntityInterface;
use App\Tests\Traits\ProfileDataTrait;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProfileOutputDataTest extends KernelTestCase
{
    use ProfileDataTrait;

    protected function setUp(): void
    {
        $this->initProfileData();
    }

    /**
     * @throws Exception
     */
    public function testConstructAndGetters(): void
    {
        $entityMock = $this->createMock(ProfileEntityInterface::class);
        $userMock   = $this->createMock(UserEntityInterface::class);

        $id     = 1;
        $userId = 1;
        $now    = new \DateTimeImmutable();

        $userMock->expects($this->once())->method('getId')->willReturn($userId);

        $entityMock->expects($this->once())->method('getId')->willReturn($id);
        $entityMock->expects($this->once())->method('getUser')->willReturn($userMock);
        $entityMock->expects($this->once())->method('getEmail')->willReturn($this->profileEmail);
        $entityMock->expects($this->once())->method('getPhone')->willReturn($this->phone);
        $entityMock->expects($this->once())->method('getFirstName')->willReturn($this->firstName);
        $entityMock->expects($this->once())->method('getLastName')->willReturn($this->lastName);
        $entityMock->expects($this->once())->method('getCreatedAt')->willReturn($now);
        $entityMock->expects($this->once())->method('getUpdatedAt')->willReturn($now);

        $dto = new ProfileOutputData($entityMock);

        $this->assertSame($id, $dto->getId());
        $this->assertSame($userId, $dto->getUserId());
        $this->assertSame((string)$this->profileEmail, $dto->getEmail());
        $this->assertSame((string)$this->phone, $dto->getPhone());
        $this->assertSame((string)$this->firstName, $dto->getFirstName());
        $this->assertSame((string)$this->lastName, $dto->getLastName());
        $this->assertSame($now, $dto->getCreatedAt());
        $this->assertSame($now, $dto->getUpdatedAt());
    }
}
