<?php

namespace App\Domain\Profile\Exception;

use App\Domain\Exception\DomainExceptionAbstract;

class ProfileLastNameInvalidException extends DomainExceptionAbstract
{
    public const ERROR_TOO_LONG = 1;
}
