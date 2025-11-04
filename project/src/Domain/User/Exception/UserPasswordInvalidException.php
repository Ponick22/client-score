<?php

namespace App\Domain\User\Exception;

use App\Domain\Exception\DomainExceptionAbstract;

class UserPasswordInvalidException extends DomainExceptionAbstract
{
    public const ERROR_TOO_SHORT    = 1;
    public const ERROR_TOO_LONG     = 2;
    public const ERROR_NO_LOWERCASE = 3;
    public const ERROR_NO_UPPERCASE = 4;
    public const ERROR_NO_NUMBER    = 5;
}
