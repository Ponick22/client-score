<?php

namespace App\Tests\Unit\Domain\User\ValueObject;

use App\Domain\User\Exception\UserPasswordInvalidException;
use App\Domain\User\ValueObject\UserPassword;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class UserPasswordTest extends TestCase
{
    public function testCreateValidPassword(): void
    {
        $password     = 'ValidPassword123';
        $userPassword = new UserPassword($password);

        $this->assertSame($password, (string)$userPassword);
    }

    #[DataProvider('invalidPasswordsProvider')]
    public function testInvalidPasswordsThrowException(string $password, int $expectedErrorCode): void
    {
        $this->expectException(UserPasswordInvalidException::class);
        $this->expectExceptionCode($expectedErrorCode);

        new UserPassword($password);
    }

    public static function invalidPasswordsProvider(): array
    {
        return [
            [
                'short',
                UserPasswordInvalidException::ERROR_TOO_SHORT,
            ],
            [
                str_repeat('a', 129),
                UserPasswordInvalidException::ERROR_TOO_LONG,
            ],
            [
                'PASSWORD123',
                UserPasswordInvalidException::ERROR_NO_LOWERCASE,
            ],
            [
                'password123',
                UserPasswordInvalidException::ERROR_NO_UPPERCASE,
            ],
            [
                'Password',
                UserPasswordInvalidException::ERROR_NO_NUMBER,
            ],
        ];
    }
}
