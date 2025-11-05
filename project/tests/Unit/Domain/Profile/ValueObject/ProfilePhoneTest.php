<?php

namespace App\Tests\Unit\Domain\Profile\ValueObject;

use App\Domain\Profile\Exception\ProfilePhoneInvalidException;
use App\Domain\Profile\ValueObject\ProfilePhone;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ProfilePhoneTest extends TestCase
{
    #[DataProvider('phoneFormatsProvider')]
    public function testPhoneFormatsNormalizeCorrectly(string $phone, string $expected): void
    {
        $profilePhone = new ProfilePhone($phone);

        $this->assertSame($expected, (string)$profilePhone);
    }

    public static function phoneFormatsProvider(): array
    {
        return [
            ['+7 911 123 45 67', '+79111234567'],
            ['89111234567', '+79111234567'],
            ['+7(911)1234567', '+79111234567'],
            ['+7-911-123-45-67', '+79111234567'],
            ['8(911)123-45-67', '+79111234567'],
            ['+7(3852)123-45-6', '+73852123456'],
        ];
    }

    #[DataProvider('invalidPhoneFormatsProvider')]
    public function testPhoneInvalidFormatsThrowException(string $phone): void
    {
        $this->expectException(ProfilePhoneInvalidException::class);
        $this->expectExceptionCode(ProfilePhoneInvalidException::ERROR_INVALID_FORMAT);

        new ProfilePhone($phone);
    }

    public static function invalidPhoneFormatsProvider(): array
    {
        return [
            ['123abc456'],
            [''],
            ['891112345678'],
            ['+7911123456'],
            ['911-123-45-67'],
        ];
    }
}
