<?php

namespace App\Application\User\Exception;

use App\Application\Exception\ApplicationExceptionAbstract;

class UserEntityNotFoundException extends ApplicationExceptionAbstract
{
    public function __construct(private readonly int $id)
    {
        parent::__construct(sprintf('User entity with ID "%s" not found', $id));
    }

    public function getId(): int
    {
        return $this->id;
    }
}
