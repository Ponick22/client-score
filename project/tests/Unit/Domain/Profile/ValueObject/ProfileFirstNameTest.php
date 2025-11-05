<?php

namespace App\Tests\Unit\Domain\Profile\ValueObject;

use App\Domain\Profile\Exception\ProfileFirstNameInvalidException;
use App\Domain\Profile\ValueObject\ProfileFirstName;
use PHPUnit\Framework\TestCase;

class ProfileFirstNameTest extends TestCase
{
    public function testCreateValidFirstName(): void
    {
        $firstName        = 'first_name';
        $profileFirstName = new ProfileFirstName($firstName);

        $this->assertSame($firstName, (string)$profileFirstName);
    }

    public function testFirstNameTooLongThrowException(): void
    {
        $this->expectException(ProfileFirstNameInvalidException::class);
        $this->expectExceptionCode(ProfileFirstNameInvalidException::ERROR_TOO_LONG);

        new ProfileFirstName(str_repeat('a', 256));
    }

}
