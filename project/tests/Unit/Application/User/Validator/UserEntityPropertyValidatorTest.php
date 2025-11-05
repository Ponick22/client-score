<?php

namespace App\Tests\Unit\Application\User\Validator;

use App\Application\User\Validator\UserEntityPropertyValidator;
use App\Domain\User\Entity\UserEntityInterface;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Tests\Traits\UserDataTrait;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserEntityPropertyValidatorTest extends KernelTestCase
{
    use UserDataTrait;

    private UserRepositoryInterface     $userRepositoryMock;
    private UserEntityPropertyValidator $validator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->userRepositoryMock = $this->createMock(UserRepositoryInterface::class);

        $this->validator = new UserEntityPropertyValidator($this->userRepositoryMock);

        $this->initUserData();
    }

    public function testEmailValidationReturnsNoErrors()
    {
        $this->userRepositoryMock->expects($this->once())
            ->method('getOneByEmail')
            ->with($this->userEmail)
            ->willReturn(null);

        $errors = $this->validator->emailValidation($this->userEmail);

        $this->assertCount(0, $errors);
    }

    /**
     * @throws Exception
     */
    public function testEmailValidationReturnsErrorWhenEmailAlreadyExists()
    {
        $this->userRepositoryMock->expects($this->once())
            ->method('getOneByEmail')
            ->with($this->userEmail)
            ->willReturn($this->createMock(UserEntityInterface::class));

        $errors = $this->validator->emailValidation($this->userEmail);

        $this->assertCount(1, $errors);
        $this->assertSame('email', $errors[0]->getProperty());
        $this->assertSame('error.user.email_already_exists', $errors[0]->getMessage());
    }
}
