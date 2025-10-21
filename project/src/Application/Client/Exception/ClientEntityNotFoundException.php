<?php

namespace App\Application\Client\Exception;

use App\Application\Exception\ApplicationExceptionAbstract;

class ClientEntityNotFoundException extends ApplicationExceptionAbstract
{
    public function __construct(private readonly int $id)
    {
        parent::__construct(
            sprintf('Client entity with ID "%s" not found', $id),
        );
    }

    public function getId(): int
    {
        return $this->id;
    }
}
