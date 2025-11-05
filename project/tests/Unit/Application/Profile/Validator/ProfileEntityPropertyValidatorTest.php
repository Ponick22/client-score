<?php

namespace App\Tests\Unit\Application\Profile\Validator;

use App\Application\Profile\Validator\ProfileEntityPropertyValidator;
use App\Domain\Profile\Entity\ProfileEntityInterface;
use App\Domain\Profile\Repository\ProfileRepositoryInterface;
use App\Domain\User\Entity\UserEntityInterface;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Tests\Traits\ProfileDataTrait;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProfileEntityPropertyValidatorTest extends KernelTestCase
{
    use ProfileDataTrait;

    private UserRepositoryInterface        $userRepositoryMock;
    private ProfileRepositoryInterface     $profileRepositoryMock;
    private ProfileEntityPropertyValidator $validator;

    private ProfileEntityInterface $profileMock;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->userRepositoryMock    = $this->createMock(UserRepositoryInterface::class);
        $this->profileRepositoryMock = $this->createMock(ProfileRepositoryInterface::class);

        $this->validator = new ProfileEntityPropertyValidator(
            $this->userRepositoryMock,
            $this->profileRepositoryMock
        );

        $this->initProfileData();

        $this->profileMock = $this->createMock(ProfileEntityInterface::class);
    }

    public function testUserValidationReturnsErrorWhenUserNotFound(): void
    {
        $userId = 1;

        $this->userRepositoryMock->expects($this->once())
            ->method('getOne')
            ->with($userId)
            ->willReturn(null);

        $this->profileRepositoryMock->expects($this->never())->method('getOneByUser');

        $errors = $this->validator->userValidation($userId);

        $this->assertCount(1, $errors);
        $this->assertSame('user', $errors[0]->getProperty());
        $this->assertSame('error.user.not_found', $errors[0]->getMessage());
    }

    /**
     * @throws Exception
     */
    public function testUserValidationReturnsErrorWhenProfileAlreadyExists(): void
    {
        $userId   = 1;
        $userMock = $this->createMock(UserEntityInterface::class);

        $this->userRepositoryMock->expects($this->once())
            ->method('getOne')
            ->with($userId)
            ->willReturn($userMock);

        $this->profileRepositoryMock->expects($this->once())
            ->method('getOneByUser')
            ->with($userMock)
            ->willReturn($this->profileMock);

        $errors = $this->validator->userValidation($userId);

        $this->assertCount(1, $errors);
        $this->assertSame('user', $errors[0]->getProperty());
        $this->assertSame('error.profile.user_already_exists', $errors[0]->getMessage());
    }

    /**
     * @throws Exception
     */
    public function testUserValidationReturnsNoErrors(): void
    {
        $userId   = 1;
        $userMock = $this->createMock(UserEntityInterface::class);

        $this->userRepositoryMock->expects($this->once())
            ->method('getOne')
            ->with($userId)
            ->willReturn($userMock);

        $this->profileRepositoryMock->expects($this->once())
            ->method('getOneByUser')
            ->with($userMock)
            ->willReturn(null);

        $errors = $this->validator->userValidation($userId);

        $this->assertCount(0, $errors);
    }

    public function testEmailValidationReturnsErrorWhenEmailExists(): void
    {
        $this->profileRepositoryMock->expects($this->once())
            ->method('getOneByEmail')
            ->with($this->profileEmail)
            ->willReturn($this->profileMock);

        $errors = $this->validator->emailValidation($this->profileEmail);

        $this->assertCount(1, $errors);
        $this->assertSame('email', $errors[0]->getProperty());
        $this->assertSame('error.email.already_exists', $errors[0]->getMessage());
    }

    public function testEmailValidationReturnsNoErrors(): void
    {
        $this->profileRepositoryMock->expects($this->once())
            ->method('getOneByEmail')
            ->with($this->profileEmail)
            ->willReturn(null);

        $errors = $this->validator->emailValidation($this->profileEmail);

        $this->assertCount(0, $errors);
    }

    public function testPhoneValidationReturnsErrorWhenPhoneExists(): void
    {
        $this->profileRepositoryMock->expects($this->once())
            ->method('getOneByPhone')
            ->with($this->phone)
            ->willReturn($this->profileMock);

        $errors = $this->validator->phoneValidation($this->phone);

        $this->assertCount(1, $errors);
        $this->assertSame('phone', $errors[0]->getProperty());
        $this->assertSame('error.phone.already_exists', $errors[0]->getMessage());
    }

    public function testPhoneValidationReturnsNoErrors(): void
    {
        $this->profileRepositoryMock->expects($this->once())
            ->method('getOneByPhone')
            ->with($this->phone)
            ->willReturn(null);

        $errors = $this->validator->phoneValidation($this->phone);

        $this->assertCount(0, $errors);
    }
}
