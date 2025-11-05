<?php

namespace App\Tests\Unit\Domain\Profile\ValueObject;

use App\Domain\Profile\Exception\ProfileLastNameInvalidException;
use App\Domain\Profile\ValueObject\ProfileLastName;
use PHPUnit\Framework\TestCase;

class ProfileLastNameTest extends TestCase
{
    public function testCreateValidFirstName(): void
    {
        $lastName        = 'last_name';
        $profileLastName = new ProfileLastName($lastName);

        $this->assertSame($lastName, (string)$profileLastName);
    }

    public function testLastNameTooLongThrowException(): void
    {
        $this->expectException(ProfileLastNameInvalidException::class);
        $this->expectExceptionCode(ProfileLastNameInvalidException::ERROR_TOO_LONG);

        new ProfileLastName(str_repeat('a', 256));
    }

}
