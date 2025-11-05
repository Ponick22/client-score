<?php

namespace App\Tests\Unit\Application\Client\Validator;

use App\Application\Client\Validator\ClientEntityPropertyValidator;
use App\Domain\Client\Entity\ClientEntityInterface;
use App\Domain\Client\Repository\ClientRepositoryInterface;
use App\Domain\Profile\Entity\ProfileEntityInterface;
use App\Domain\Profile\Repository\ProfileRepositoryInterface;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ClientEntityPropertyValidatorTest extends KernelTestCase
{
    private ClientRepositoryInterface     $clientRepositoryMock;
    private ProfileRepositoryInterface    $profileRepositoryMock;
    private ClientEntityPropertyValidator $validator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->clientRepositoryMock  = $this->createMock(ClientRepositoryInterface::class);
        $this->profileRepositoryMock = $this->createMock(ProfileRepositoryInterface::class);

        $this->validator = new ClientEntityPropertyValidator(
            $this->clientRepositoryMock,
            $this->profileRepositoryMock
        );
    }

    public function testProfileValidationReturnsErrorWhenProfileNotFound(): void
    {
        $profileId = 1;

        $this->profileRepositoryMock->expects($this->once())
            ->method('getOne')
            ->with($profileId)
            ->willReturn(null);

        $this->clientRepositoryMock->expects($this->never())->method('getOneByProfile');

        $errors = $this->validator->profileValidation($profileId);

        $this->assertCount(1, $errors);
        $this->assertSame('profile', $errors[0]->getProperty());
        $this->assertSame('error.profile.not_found', $errors[0]->getMessage());
    }

    /**
     * @throws Exception
     */
    public function testProfileValidationReturnsErrorWhenClientAlreadyExists(): void
    {
        $profileId = 1;

        $profileMock = $this->createMock(ProfileEntityInterface::class);

        $this->profileRepositoryMock->expects($this->once())
            ->method('getOne')
            ->with($profileId)
            ->willReturn($profileMock);

        $this->clientRepositoryMock->expects($this->once())
            ->method('getOneByProfile')
            ->with($profileMock)
            ->willReturn($this->createMock(ClientEntityInterface::class));

        $errors = $this->validator->profileValidation($profileId);

        $this->assertCount(1, $errors);
        $this->assertSame('profile', $errors[0]->getProperty());
        $this->assertSame('error.client.profile_already_exists', $errors[0]->getMessage());
    }

    /**
     * @throws Exception
     */
    public function testProfileValidationReturnsNoErrors(): void
    {
        $profileId = 1;

        $profileMock = $this->createMock(ProfileEntityInterface::class);

        $this->profileRepositoryMock->expects($this->once())
            ->method('getOne')
            ->with($profileId)
            ->willReturn($profileMock);

        $this->clientRepositoryMock->expects($this->once())
            ->method('getOneByProfile')
            ->with($profileMock)
            ->willReturn(null);

        $errors = $this->validator->profileValidation($profileId);

        $this->assertCount(0, $errors);
    }
}
