<?php

namespace App\Tests\Unit\Application\Profile;

use App\Application\Profile\DTO\ProfileCreateData;
use App\Application\Profile\ProfileEntityCreator;
use App\Domain\EntityManager\EntityManagerInterface;
use App\Domain\EntityManager\Exception\EntityManagerException;
use App\Domain\Profile\Entity\ProfileEntityInterface;
use App\Domain\Profile\Factory\ProfileEntityFactoryInterface;
use App\Domain\User\Entity\UserEntityInterface;
use App\Tests\Traits\ProfileDataTrait;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProfileEntityCreatorTest extends KernelTestCase
{
    use ProfileDataTrait;

    private ProfileEntityFactoryInterface $factoryMock;
    private EntityManagerInterface        $entityManagerMock;
    private ProfileEntityCreator          $creator;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->factoryMock       = $this->createMock(ProfileEntityFactoryInterface::class);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);

        $this->creator = new ProfileEntityCreator(
            $this->factoryMock,
            $this->entityManagerMock
        );

        $this->initProfileData();
    }

    /**
     * @throws Exception
     * @throws EntityManagerException
     */
    public function testCreateProfile(): void
    {
        $userMock = $this->createMock(UserEntityInterface::class);

        $createData = new ProfileCreateData(
            $userMock,
            $this->profileEmail,
            $this->phone,
            $this->firstName,
            $this->lastName
        );

        $entityMock = $this->createMock(ProfileEntityInterface::class);
        $this->factoryMock->expects($this->once())->method('create')->willReturn($entityMock);

        $entityMock->expects($this->once())->method('setUser')->with($userMock)->willReturnSelf();
        $entityMock->expects($this->once())->method('setEmail')->with($this->profileEmail)->willReturnSelf();
        $entityMock->expects($this->once())->method('setPhone')->with($this->phone)->willReturnSelf();
        $entityMock->expects($this->once())->method('setFirstName')->with($this->firstName)->willReturnSelf();
        $entityMock->expects($this->once())->method('setLastName')->with($this->lastName)->willReturnSelf();

        $this->entityManagerMock->expects($this->once())->method('persist')->with($entityMock);

        $result = $this->creator->create($createData);

        $this->assertSame($entityMock, $result);
    }
}
