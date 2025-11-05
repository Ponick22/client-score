<?php

namespace App\Tests\Unit\Domain\User\ValueObject;

use App\Domain\User\Exception\UserEmailInvalidException;
use App\Domain\User\ValueObject\UserEmail;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class UserEmailTest extends TestCase
{
    public function testCreateValidEmail(): void
    {
        $email     = 'user@example.com';
        $userEmail = new UserEmail($email);

        $this->assertSame($email, (string)$userEmail);
    }

    #[DataProvider('invalidEmailProvider')]
    public function testInvalidEmailsThrowException(string $email, int $expectedErrorCode): void
    {
        $this->expectException(UserEmailInvalidException::class);
        $this->expectExceptionCode($expectedErrorCode);

        new UserEmail($email);
    }

    public static function invalidEmailProvider(): array
    {
        return [
            [
                str_repeat('a', 256),
                UserEmailInvalidException::ERROR_TOO_LONG,
            ],
            [
                'email',
                UserEmailInvalidException::ERROR_INVALID_FORMAT,
            ],
        ];
    }
}
