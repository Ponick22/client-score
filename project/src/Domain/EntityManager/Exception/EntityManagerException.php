<?php

namespace App\Domain\EntityManager\Exception;

use App\Domain\Exception\DomainExceptionAbstract;

class EntityManagerException extends DomainExceptionAbstract
{
    public const ERROR_PERSIST              = 1;
    public const ERROR_FLUSH                = 2;
    public const ERROR_REMOVE               = 3;
    public const ERROR_CLEAR                = 4;
    public const ERROR_TRANSACTION_BEGIN    = 5;
    public const ERROR_TRANSACTION_COMMIT   = 6;
    public const ERROR_TRANSACTION_ROLLBACK = 7;
}
