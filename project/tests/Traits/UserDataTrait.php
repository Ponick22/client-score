<?php

namespace App\Tests\Traits;

use App\Domain\User\Enum\UserRoleEnum;
use App\Domain\User\ValueObject\UserEmail;
use App\Domain\User\ValueObject\UserPassword;
use App\Domain\User\ValueObject\UserRoleEnumCollection;

trait UserDataTrait
{
    private UserEmail              $userEmail;
    private UserRoleEnumCollection $roles;
    private UserPassword           $password;

    private function initUserData(): void
    {
        $this->userEmail = new UserEmail('user@example.com');
        $this->password  = new UserPassword('Password123');
        $this->roles     = new UserRoleEnumCollection([UserRoleEnum::Client]);
    }
}
