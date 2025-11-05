<?php

namespace App\Tests\Unit\Domain\User\ValueObject;

use App\Domain\User\ValueObject\UserHashPassword;
use PHPUnit\Framework\TestCase;

class UserHashPasswordTest extends TestCase
{
    public function testCreateFromHashPassword(): void
    {
        $hash             = password_hash('password', PASSWORD_BCRYPT);
        $userHashPassword = UserHashPassword::fromHashPassword($hash);

        $this->assertSame($hash, (string)$userHashPassword);
    }

    public function testThrowInvalidArgumentExceptionForNonHashedPassword(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        UserHashPassword::fromHashPassword('123456789');
    }
}
