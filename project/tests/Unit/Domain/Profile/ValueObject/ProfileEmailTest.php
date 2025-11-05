<?php

namespace App\Tests\Unit\Domain\Profile\ValueObject;

use App\Domain\Profile\Exception\ProfileEmailInvalidException;
use App\Domain\Profile\ValueObject\ProfileEmail;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ProfileEmailTest extends TestCase
{
    public function testCreateValidEmail(): void
    {
        $email        = 'profile@example.com';
        $profileEmail = new ProfileEmail($email);

        $this->assertSame($email, (string)$profileEmail);
    }

    #[DataProvider('invalidEmailProvider')]
    public function testInvalidEmailsThrowException(string $email, int $expectedErrorCode): void
    {
        $this->expectException(ProfileEmailInvalidException::class);
        $this->expectExceptionCode($expectedErrorCode);

        new ProfileEmail($email);
    }

    public static function invalidEmailProvider(): array
    {
        return [
            [
                str_repeat('a', 256),
                ProfileEmailInvalidException::ERROR_TOO_LONG,
            ],
            [
                'email',
                ProfileEmailInvalidException::ERROR_INVALID_FORMAT,
            ],
        ];
    }
}
