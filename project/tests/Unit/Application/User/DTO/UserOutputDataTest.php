<?php

namespace App\Tests\Unit\Application\User\DTO;

use App\Application\User\DTO\UserOutputData;
use App\Domain\User\Entity\UserEntityInterface;
use App\Tests\Traits\UserDataTrait;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserOutputDataTest extends KernelTestCase
{
    use UserDataTrait;

    protected function setUp(): void
    {
        $this->initUserData();
    }

    /**
     * @throws Exception
     */
    public function testConstructAndGetters(): void
    {
        $entityMock = $this->createMock(UserEntityInterface::class);

        $id  = 1;
        $now = new \DateTimeImmutable();

        $entityMock->expects($this->once())->method('getId')->willReturn($id);
        $entityMock->expects($this->once())->method('getEmail')->willReturn($this->userEmail);
        $entityMock->expects($this->once())->method('getRoles')->willReturn($this->roles);
        $entityMock->expects($this->once())->method('getCreatedAt')->willReturn($now);
        $entityMock->expects($this->once())->method('getUpdatedAt')->willReturn($now);

        $dto = new UserOutputData($entityMock);

        $this->assertSame($id, $dto->getId());
        $this->assertSame((string)$this->userEmail, $dto->getEmail());
        $this->assertSame($this->roles->toArray(), $dto->getRoles());
        $this->assertSame($now, $dto->getCreatedAt());
        $this->assertSame($now, $dto->getUpdatedAt());
    }
}
