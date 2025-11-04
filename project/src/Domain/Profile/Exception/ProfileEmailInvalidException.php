<?php

namespace App\Domain\Profile\Exception;

use App\Domain\Exception\DomainExceptionAbstract;

class ProfileEmailInvalidException extends DomainExceptionAbstract
{
    public const ERROR_TOO_LONG       = 1;
    public const ERROR_INVALID_FORMAT = 2;
}
