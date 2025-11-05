<?php

namespace App\Tests\Unit\Application\Profile;

use App\Application\Profile\Exception\ProfileEntityNotFoundException;
use App\Application\Profile\ProfileEntityGetter;
use App\Domain\Profile\Entity\ProfileEntityInterface;
use App\Domain\Profile\Repository\ProfileRepositoryInterface;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProfileEntityGetterTest extends KernelTestCase
{
    private ProfileRepositoryInterface $repositoryMock;
    private ProfileEntityGetter        $getter;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(ProfileRepositoryInterface::class);

        $this->getter = new ProfileEntityGetter($this->repositoryMock);
    }

    /**
     * @throws ProfileEntityNotFoundException
     * @throws Exception
     */
    public function testGetReturnsEntity(): void
    {
        $profileId = 1;

        $entity = $this->createStub(ProfileEntityInterface::class);

        $this->repositoryMock->expects($this->once())
            ->method('getOne')
            ->with($profileId)
            ->willReturn($entity);

        $result = $this->getter->get($profileId);

        $this->assertSame($entity, $result);
    }

    public function testGetThrowsExceptionWhenEntityNotFound(): void
    {
        $profileId = 100;

        $this->repositoryMock->expects($this->once())
            ->method('getOne')
            ->with($profileId)
            ->willReturn(null);

        $this->expectException(ProfileEntityNotFoundException::class);

        $this->getter->get($profileId);
    }
}
