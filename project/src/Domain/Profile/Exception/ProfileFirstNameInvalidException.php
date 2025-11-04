<?php

namespace App\Domain\Profile\Exception;

use App\Domain\Exception\DomainExceptionAbstract;

class ProfileFirstNameInvalidException extends DomainExceptionAbstract
{
    public const ERROR_TOO_LONG = 1;
}
