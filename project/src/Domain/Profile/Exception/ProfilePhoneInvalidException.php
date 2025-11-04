<?php

namespace App\Domain\Profile\Exception;

use App\Domain\Exception\DomainExceptionAbstract;

class ProfilePhoneInvalidException extends DomainExceptionAbstract
{
    public const ERROR_INVALID_FORMAT = 1;
}
