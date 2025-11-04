<?php

namespace App\Domain\User\Exception;

use App\Domain\Exception\DomainExceptionAbstract;

class UserEmailInvalidException extends DomainExceptionAbstract
{
    public const ERROR_TOO_LONG       = 1;
    public const ERROR_INVALID_FORMAT = 2;
}
