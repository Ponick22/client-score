<?php

namespace App\Application\Profile\Exception;

use App\Application\Exception\ApplicationExceptionAbstract;

class ProfileEntityNotFoundException extends ApplicationExceptionAbstract
{
    public function __construct(private readonly int $id)
    {
        parent::__construct(sprintf('Profile entity with ID "%s" not found', $id));
    }

    public function getId(): int
    {
        return $this->id;
    }
}
