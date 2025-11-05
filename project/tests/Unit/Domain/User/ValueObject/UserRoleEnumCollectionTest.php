<?php

namespace App\Tests\Unit\Domain\User\ValueObject;

use App\Domain\User\Enum\UserRoleEnum;
use App\Domain\User\ValueObject\UserRoleEnumCollection;
use PHPUnit\Framework\TestCase;

class UserRoleEnumCollectionTest extends TestCase
{
    public function testGetClassReturnsAndToArray(): void
    {
        $collection = new UserRoleEnumCollection([UserRoleEnum::Client]);
        $collection->add(UserRoleEnum::Admin);

        $this->assertSame(UserRoleEnum::class, $collection->getClass());
        $this->assertSame(
            [
                UserRoleEnum::Client->value,
                UserRoleEnum::Admin->value,
            ],
            $collection->toArray()
        );
    }
}
